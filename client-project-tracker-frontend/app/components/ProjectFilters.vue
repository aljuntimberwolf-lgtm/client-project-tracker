<script setup lang="ts">
import { PRIORITY_OPTIONS, SORT_OPTIONS, STATUS_OPTIONS } from '~/constants/project'

const search = defineModel<string>('search', { default: '' })
const statusFilter = defineModel<string>('statusFilter', { default: '' })
const priorityFilter = defineModel<string>('priorityFilter', { default: '' })
const sortBy = defineModel<string>('sortBy', { default: '' })
</script>

<template>
  <section class="card filters-card">

    <!-- Search -->
    <div class="filter-item search-item">
      <label for="search">Search</label>
      <input
        id="search"
        v-model="search"
        type="text"
        placeholder="Search client, project, or description..."
      />
    </div>

    <!-- Status -->
    <div class="filter-item">
      <label for="status-filter">Status</label>
      <select
        id="status-filter"
        v-model="statusFilter"
      >
        <option value="">All Statuses</option>
        <option
          v-for="option in STATUS_OPTIONS"
          :key="option"
          :value="option"
        >
          {{ option }}
        </option>
      </select>
    </div>

    <!-- Priority -->
    <div class="filter-item">
      <label for="priority-filter">Priority</label>
      <select
        id="priority-filter"
        v-model="priorityFilter"
      >
        <option value="">All Priorities</option>
        <option
          v-for="option in PRIORITY_OPTIONS"
          :key="option"
          :value="option"
        >
          {{ option }}
        </option>
      </select>
    </div>

    <!-- Sort -->
    <div class="filter-item">
      <label for="sort">Sort By</label>
      <select
        id="sort"
        v-model="sortBy"
      >
        <option
          v-for="option in SORT_OPTIONS"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
    </div>

  </section>
</template>