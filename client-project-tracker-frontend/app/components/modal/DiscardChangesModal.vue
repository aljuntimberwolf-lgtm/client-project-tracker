<script setup lang="ts">
const emit = defineEmits<{
  discard: []
  keep: []
}>()

const dialog = ref<HTMLElement | null>(null)

onMounted(() => dialog.value?.focus())

function onOverlayClick(event: MouseEvent) {
  if (event.target === event.currentTarget) {
    emit('keep')
  }
}

useModalDismiss(() => emit('keep'))
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
      aria-labelledby="discard-title"
      aria-describedby="discard-description"
      tabindex="-1"
    >
      <h3 id="discard-title">Discard unsaved changes?</h3>

      <p id="discard-description">
        Your edits to this project haven't been saved yet.
        If you close now, they'll be lost.
      </p>

      <div class="confirm-actions">
        <button
          type="button"
          class="cancel-button"
          @click="emit('keep')"
        >
          Keep editing
        </button>

        <button
          type="button"
          class="confirm-button"
          @click="emit('discard')"
        >
          Discard changes
        </button>
      </div>
    </div>
  </div>
</template>