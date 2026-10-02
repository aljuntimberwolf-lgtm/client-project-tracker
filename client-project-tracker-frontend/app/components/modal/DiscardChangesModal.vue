<script setup lang="ts">
const emit = defineEmits<{
  discard: []
  keep: []
}>()

const dialog = ref<HTMLElement | null>(null)

onMounted(() => {
  dialog.value?.focus()
})

function onOverlayClick(event: MouseEvent) {
  if (event.target === event.currentTarget) {
    emit('keep')
  }
}

function onKeydown(event: KeyboardEvent) {
  if (event.key === 'Escape') {
    emit('keep')
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      class="discard-overlay"
      @click="onOverlayClick"
    >
      <div
        ref="dialog"
        class="discard-dialog"
        role="alertdialog"
        aria-modal="true"
        aria-labelledby="discard-title"
        aria-describedby="discard-description"
        tabindex="-1"
      >

        <h3 id="discard-title">
          Discard unsaved changes?
        </h3>

        <p id="discard-description">
          Your edits to this project haven't been saved yet.
          If you close now, they'll be lost.
        </p>

        <div class="discard-actions">

          <button
            type="button"
            class="keep-button"
            @click="emit('keep')"
          >
            Keep editing
          </button>

          <button
            type="button"
            class="discard-button"
            @click="emit('discard')"
          >
            Discard changes
          </button>

        </div>

      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.discard-overlay {
  position: fixed;
  inset: 0;
  z-index: 60;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px 16px;
  background: rgba(15, 23, 42, 0.6);
}

.discard-dialog {
  width: 100%;
  max-width: 420px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 20px 45px rgba(15, 23, 42, 0.25);
  padding: 25px;
  outline: none;
}

.discard-dialog h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 700;
  color: #172033;
}

.discard-dialog p {
  margin: 10px 0 22px;
  font-size: 14px;
  line-height: 1.5;
  color: #475569;
}

.discard-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.keep-button,
.discard-button {
  border: none;
  border-radius: 6px;
  padding: 10px 16px;
  font-size: 14px;
  font-weight: 600;
  transition: 0.2s ease;
}

.keep-button {
  background: #e5e7eb;
  color: #374151;
}

.keep-button:hover {
  background: #d1d5db;
}

.discard-button {
  background: #dc2626;
  color: white;
}

.discard-button:hover {
  background: #b91c1c;
}

@media (max-width: 700px) {
  .discard-actions {
    flex-direction: column-reverse;
  }

  .keep-button,
  .discard-button {
    width: 100%;
  }
}
</style>
