<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\UniformItemVariants;
use App\Models\UniformItems;

class UniqueVariantSize implements ValidationRule
{
    public function __construct(
        protected ?string $itemName,
        protected array $allSizes,       // all sizes from the form (including current)
        protected ?int $currentVariantId = null, // the ID of the variant being edited
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (blank($value)) return;

        // 1️⃣ Check for duplicates within the repeater itself
        //    Count how many times this value appears in allSizes
        $countInForm = collect($this->allSizes)
            ->filter(fn($s) => strtolower(trim($s)) === strtolower(trim($value)))
            ->count();

        if ($countInForm > 1) {
            $fail("The size \"{$value}\" is duplicated in this form.");
            return;
        }

        // 2️⃣ Check uniqueness in the database for this item
        if (blank($this->itemName)) return;

        $item = UniformItems::where('uniform_item_name', $this->itemName)->first();
        if (!$item) return;

        $exists = UniformItemVariants::where('uniform_item_id', $item->id)
            ->whereRaw('LOWER(TRIM(uniform_item_size)) = ?', [strtolower(trim($value))])
            ->when(
                $this->currentVariantId,
                fn($q) => $q->where('id', '!=', $this->currentVariantId) // ignore self on edit
            )
            ->exists();

        if ($exists) {
            $fail("The size \"{$value}\" already exists for this uniform item.");
        }
    }
}