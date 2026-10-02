<script setup lang="ts">
const search = defineModel<string>('search', {
  default: ''
})

const statusFilter = defineModel<string>('statusFilter', {
  default: ''
})

const priorityFilter = defineModel<string>('priorityFilter', {
  default: ''
})

const sortBy = defineModel<string>('sortBy', {
  default: ''
})
</script>

<template>
  <section class="card filters-card">

    <!-- Search -->
    <div class="filter-item search-item">
      <label for="search">
        Search
      </label>

      <input
        id="search"
        v-model="search"
        type="text"
        placeholder="Search client, project, or description..."
      />
    </div>

    <!-- Status -->
    <div class="filter-item">
      <label for="status-filter">
        Status
      </label>

      <select
        id="status-filter"
        v-model="statusFilter"
      >
        <option value="">
          All Statuses
        </option>

        <option value="Planning">
          Planning
        </option>

        <option value="In Progress">
          In Progress
        </option>

        <option value="On Hold">
          On Hold
        </option>

        <option value="Completed">
          Completed
        </option>
      </select>
    </div>

    <!-- Priority -->
    <div class="filter-item">
      <label for="priority-filter">
        Priority
      </label>

      <select
        id="priority-filter"
        v-model="priorityFilter"
      >
        <option value="">
          All Priorities
        </option>

        <option value="Low">
          Low
        </option>

        <option value="Medium">
          Medium
        </option>

        <option value="High">
          High
        </option>
      </select>
    </div>

    <!-- Sort -->
    <div class="filter-item">
      <label for="sort">
        Sort By
      </label>

      <select
        id="sort"
        v-model="sortBy"
      >
        <option value="">
          Default
        </option>

        <option value="client_asc">
          Client Name A → Z
        </option>

        <option value="client_desc">
          Client Name Z → A
        </option>

        <option value="project_asc">
          Project Name A → Z
        </option>

        <option value="project_desc">
          Project Name Z → A
        </option>

        <option value="start_asc">
          Start Date — Oldest
        </option>

        <option value="start_desc">
          Start Date — Newest
        </option>

        <option value="due_asc">
          Due Date — Earliest
        </option>

        <option value="due_desc">
          Due Date — Latest
        </option>

        <option value="priority_asc">
          Priority — Low to High
        </option>

        <option value="priority_desc">
          Priority — High to Low
        </option>
      </select>
    </div>

  </section>
</template>