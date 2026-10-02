<script setup lang="ts">
defineProps<{
  projects: any[]
}>()

const emit = defineEmits<{
  edit: [project: any]
  delete: [id: number]
}>()
</script>

<template>
  <div class="table-wrapper">

    <table>

      <thead>
        <tr>
          <th>ID</th>
          <th>Client</th>
          <th>Project</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Start Date</th>
          <th>Due Date</th>
          <th class="actions-column">
            Actions
          </th>
        </tr>
      </thead>

      <tbody>

        <tr
          v-for="project in projects"
          :key="project.id"
        >

          <td class="id-column">
            {{ project.id }}
          </td>

          <td>
            <strong>
              {{ project.client_name }}
            </strong>
          </td>

          <td>
            {{ project.project_name }}
          </td>

          <!-- Status -->
          <td>

            <span
              class="badge status-badge"
              :class="{
                'status-planning':
                  project.status === 'Planning',

                'status-progress':
                  project.status === 'In Progress',

                'status-hold':
                  project.status === 'On Hold',

                'status-completed':
                  project.status === 'Completed'
              }"
            >
              {{ project.status }}
            </span>

          </td>

          <!-- Priority -->
          <td>

            <span
              class="badge priority-badge"
              :class="{
                'priority-low':
                  project.priority === 'Low',

                'priority-medium':
                  project.priority === 'Medium',

                'priority-high':
                  project.priority === 'High'
              }"
            >
              {{ project.priority }}
            </span>

          </td>

          <td>
            {{ project.start_date.substring(0, 10) }}
          </td>

          <td>
            {{ project.due_date.substring(0, 10) }}
          </td>

          <!-- Actions -->
          <td class="actions">

            <button
              class="edit-button"
              @click="emit('edit', project)"
            >
              Edit
            </button>

            <button
              class="delete-button"
              @click="emit('delete', project.id)"
            >
              Delete
            </button>

          </td>

        </tr>

      </tbody>

    </table>

  </div>
</template>