<script setup lang="ts" name="FormSelectField">
interface Props {
  name: string;
  title: string;
  small?: boolean;
  required?: boolean;
  error?: string;
  options: {
    label: string;
    value: string;
  }[];
}

const {
  name,
  title,
  small = false,
  required = false,
  error = '',
  options = [],
} = defineProps<Props>();

const modelValue = defineModel<string>();
</script>

<template>
  <label :for="name" class="flex flex-col items-center sm:flex-row">
    <span class="min-w-[120px] md:mr-20">
      {{ title }}
      <span v-if="required" class="text-red-500">*</span>
    </span>

    <div>
      <select
        :id="name"
        :required="required"
        v-model="modelValue"
        :class="{ 'w-32': small, 'w-full md:w-96': !small }"
        class="rounded-md border p-1 outline-none hover:shadow-input-hover focus:shadow-input-focus"
      >
        <option
          v-for="option in options"
          :key="option.label"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
      <p v-if="error" class="mt-1 text-xs text-red-500">
        {{ error }}
      </p>
    </div>
  </label>
</template>
