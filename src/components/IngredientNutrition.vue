<script setup lang="ts" name="IngredientNutrition">
import { useIngredientNutritionalInfo } from '@/features/useIngredientNutritionalInfo';
import { Ingredient } from '@/features/useTypes';

const { ingredient } = defineProps<{
  ingredient: Ingredient;
}>();

const nutritionalInfo = $computed(() =>
  useIngredientNutritionalInfo(ingredient),
);
</script>

<template>
  <div class="flex w-1/2 flex-col space-y-10">
    <div class="max-w-max">
      <h5 class="text-lg font-semibold">
        Nutritional information per {{ ingredient.serving_name }}
      </h5>

      <div class="mt-6 flex justify-between">
        <p>One {{ ingredient.serving_name }}:</p>
        <p>{{ ingredient.serving_grams }} g</p>
      </div>

      <div class="text-sm font-light">
        <div
          v-for="item in nutritionalInfo.items"
          :key="item.label"
          class="flex items-center justify-between"
        >
          <p>{{ item.label }}:</p>
          <p>{{ item.value }}{{ item.postfix }}</p>
        </div>
      </div>
    </div>

    <div>
      <!-- <EChart class="h-72" :option="nutritionalInfo.chartOptions" /> -->
    </div>
  </div>
</template>
