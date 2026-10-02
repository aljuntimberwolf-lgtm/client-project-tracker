<script setup lang="ts">
import type { Project } from '~/types/project'

const props = defineProps<{
  project: Project
  error?: string
  pending?: boolean
}>()

const emit = defineEmits<{
  confirm: []
  cancel: []
}>()

const dialog = ref<HTMLElement | null>(null)

onMounted(() => dialog.value?.focus())

function cancel() {
  if (props.pending) {
    return
  }

  emit('cancel')
}

function onOverlayClick(event: MouseEvent) {
  if (event.target === event.currentTarget) {
    cancel()
  }
}

useModalDismiss(cancel)
</script>

<template>
  <div
    class="confirm-overlay"
    @click="onOverlayClick"
  >
    <div
      ref="dialog"
      class="confirm-dialog dialog-panel"
      role="alertdialog"
      aria-modal="true"
      aria-labelledby="delete-title"
      aria-describedby="delete-description"
      tabindex="-1"
    >
      <h3 id="delete-title">Delete project?</h3>

      <p id="delete-description">
        You're about to delete
        <strong>{{ project.project_name }}</strong>
        for {{ project.client_name }}. This action can't be
        undone.
      </p>

      <div
        v-if="error"
        class="error-message"
      >
        {{ error }}
      </div>

      <div class="confirm-actions">
        <button
          type="button"
          class="cancel-button"
          :disabled="pending"
          @click="cancel"
        >
          Cancel
        </button>

        <button
          type="button"
          class="confirm-button"
          :disabled="pending"
          @click="emit('confirm')"
        >
          <span
            v-if="pending"
            class="button-spinner"
            aria-hidden="true"
          />
          {{ pending ? 'Deleting...' : 'Delete project' }}
        </button>
      </div>
    </div>
  </div>
</template>