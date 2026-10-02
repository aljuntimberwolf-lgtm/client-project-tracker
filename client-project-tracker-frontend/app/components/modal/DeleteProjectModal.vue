<script setup lang="ts">
const props = defineProps<{
  project: any
  error?: string
  pending?: boolean
}>()

const emit = defineEmits<{
  confirm: []
  cancel: []
}>()

const dialog = ref<HTMLElement | null>(null)

onMounted(() => {
  dialog.value?.focus()
})

function onOverlayClick(event: MouseEvent) {
  if (event.target === event.currentTarget) {
    cancel()
  }
}

function cancel() {
  if (props.pending) {
    return
  }

  emit('cancel')
}

function onKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape') {
    cancel()
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      class="delete-overlay"
      @click="onOverlayClick"
    >
      <div
        ref="dialog"
        class="delete-dialog"
        role="alertdialog"
        aria-modal="true"
        aria-labelledby="delete-title"
        aria-describedby="delete-description"
        tabindex="-1"
      >

        <h3 id="delete-title">
          Delete project?
        </h3>

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

        <div class="delete-actions">

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
            class="delete-button"
            :disabled="pending"
            @click="emit('confirm')"
          >
            <span
              v-if="pending"
              class="button-spinner"
              aria-hidden="true"
            ></span>

            {{ pending ? 'Deleting...' : 'Delete project' }}
          </button>

        </div>

      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.delete-overlay {
  position: fixed;
  inset: 0;
  z-index: 60;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px 16px;
  background: rgba(15, 23, 42, 0.6);
}

.delete-dialog {
  width: 100%;
  max-width: 420px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 20px 45px rgba(15, 23, 42, 0.25);
  padding: 25px;
  outline: none;
}

.delete-dialog h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  color: #172033;
}

.delete-dialog p {
  margin: 10px 0 22px;
  font-size: 14px;
  line-height: 1.5;
  color: #475569;
}

.delete-dialog p strong {
  color: #172033;
}

.delete-dialog .error-message {
  margin: -8px 0 18px;
}

.delete-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.cancel-button,
.delete-button {
  border: none;
  border-radius: 6px;
  padding: 10px 16px;
  font-size: 14px;
  font-weight: 600;
  transition: 0.2s ease;
}

.cancel-button {
  background: #e5e7eb;
  color: #374151;
}

.cancel-button:hover {
  background: #d1d5db;
}

.delete-button {
  background: #dc2626;
  color: white;
}

.delete-button:hover {
  background: #b91c1c;
}

.cancel-button:disabled,
.delete-button:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.button-spinner {
  display: inline-block;
  width: 14px;
  height: 14px;
  margin-right: 8px;
  vertical-align: -2px;
  border: 2px solid rgba(255, 255, 255, 0.35);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 700px) {
  .delete-actions {
    flex-direction: column-reverse;
  }

  .cancel-button,
  .delete-button {
    width: 100%;
  }
}
</style>
