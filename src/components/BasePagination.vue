<script setup lang="ts" name="BasePagination">
import { paginationLink } from '@/features/useTypes';

const { links } = defineProps<{
  links: paginationLink[];
}>();
</script>

<template>
  <div class="flex space-x-4">
    <span
      v-for="link in links"
      :key="link.label"
      :href="link.url"
      v-html="link.label"
      :class="{
        'text-gray-500': !link.url,
        'hover:text-pink-500': link.url,
        'text-pink-500': link.active,
      }"
    />
  </div>
</template>
