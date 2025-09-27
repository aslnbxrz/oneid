<?php

namespace Aslnbxrz\OneId\Data;

use Spatie\LaravelData\Data;

/**
 * OneID User Data - Rasmiy hujjatga mos keluvchi foydalanuvchi ma'lumotlari
 * 
 * Bu class OneID rasmiy texnik hujjatidagi barcha user data fieldlarini o'z ichiga oladi
 */
class OneIDUserData extends Data
{
    /**
     * Jismoniy shaxs ma'lumotlari
     */
    public ?bool $valid = null;
    public ?array $validation_method = null;
    public ?string $pin = null;
    public ?string $user_id = null;
    public ?string $full_name = null;
    public ?string $pport_no = null;
    public ?string $birth_date = null;
    public ?string $sur_name = null;
    public ?string $first_name = null;
    public ?string $mid_name = null;
    public ?string $user_type = null;
    public ?string $sess_id = null;
    public ?string $ret_cd = null;
    public ?string $auth_method = null;
    public ?string $pkcs_legal_tin = null;

    /**
     * Yuridik shaxs ma'lumotlari
     */
    public ?array $legal_info = null;

    /**
     * Helper methodlar
     */
    
    /**
     * Foydalanuvchi tasdiqlangan maqomda ekanligini tekshirish
     */
    public function isVerified(): bool
    {
        return $this->valid === true && !empty($this->validation_method);
    }

    /**
     * Foydalanuvchi jismoniy shaxs ekanligini tekshirish
     */
    public function isIndividual(): bool
    {
        return $this->user_type === 'I';
    }

    /**
     * Foydalanuvchi yuridik shaxs ekanligini tekshirish
     */
    public function isLegalEntity(): bool
    {
        return $this->user_type === 'L';
    }

    /**
     * Avtorizatsiya muvaffaqiyatli o'tganini tekshirish
     */
    public function isAuthorized(): bool
    {
        return $this->ret_cd === '0';
    }

    /**
     * Asosiy yuridik shaxs ma'lumotlarini olish
     */
    public function getBasicLegalEntity(): ?array
    {
        if (empty($this->legal_info)) {
            return null;
        }

        foreach ($this->legal_info as $entity) {
            if (isset($entity['is_basic']) && $entity['is_basic'] === true) {
                return $entity;
            }
        }

        // Agar asosiy topilmasa, birinchi yuridik shaxsni qaytarish
        return $this->legal_info[0] ?? null;
    }

    /**
     * To'liq ismni olish
     */
    public function getFullName(): string
    {
        if ($this->full_name) {
            return $this->full_name;
        }

        $name = $this->sur_name ?? '';
        $name .= $this->first_name ? ' ' . $this->first_name : '';
        $name .= $this->mid_name ? ' ' . $this->mid_name : '';

        return trim($name);
    }

    /**
     * Tug'ilgan sanani DateTime formatida olish
     */
    public function getBirthDate(): ?\DateTime
    {
        if (!$this->birth_date) {
            return null;
        }

        try {
            // OneID format: YYYYMMDD
            if (strlen($this->birth_date) === 8) {
                return \DateTime::createFromFormat('Ymd', $this->birth_date);
            }
            
            // Alternative format: YYYY-MM-DD
            if (strlen($this->birth_date) === 10) {
                return new \DateTime($this->birth_date);
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * PIN raqamini tekshirish
     */
    public function isValidPin(): bool
    {
        return $this->pin && strlen($this->pin) === 14 && ctype_digit($this->pin);
    }

    /**
     * Avtorizatsiya usulini tekshirish
     */
    public function getAuthMethodName(): string
    {
        return match($this->auth_method) {
            'LOGINPASSMETHOD' => 'Login va Parol',
            'MOBILEMETHOD' => 'Mobile-ID',
            'PKCSMETHOD' => 'Elektron raqamli imzo (ERI)',
            'LEPKCSMETHOD' => 'Yuridik shaxs ERI',
            'QR' => 'QR kod',
            default => 'Noma\'lum usul'
        };
    }

    /**
     * Tasdiqlash usulini tekshirish
     */
    public function getValidationMethods(): array
    {
        $methods = [];
        
        if (empty($this->validation_method)) {
            return $methods;
        }

        foreach ($this->validation_method as $method) {
            $methods[] = match($method) {
                'PKCSMETHOD' => 'Elektron raqamli imzo (ERI)',
                'MOBILEMETHOD' => 'Mobile-ID',
                default => 'Noma\'lum usul'
            };
        }

        return $methods;
    }
}
