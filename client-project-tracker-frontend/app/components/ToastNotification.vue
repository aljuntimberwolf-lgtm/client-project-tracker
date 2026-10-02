<script setup lang="ts">
import type { Toast } from '~/types/toast'

defineProps<{
  toasts: Toast[]
}>()

const emit = defineEmits<{
  dismiss: [id: number]
}>()
</script>

<template>
  <div
    class="toast-stack"
    role="status"
    aria-live="polite"
  >
    <TransitionGroup name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="toast"
      >
        <span
          class="toast-icon"
          aria-hidden="true"
        >&#10003;</span>

        <p class="toast-message">{{ toast.message }}</p>

        <button
          type="button"
          class="toast-close"
          aria-label="Dismiss notification"
          @click="emit('dismiss', toast.id)"
        >
          &times;
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.toast-stack {
  position: fixed;
  right: 20px;
  bottom: 20px;
  z-index: 70;
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
  max-width: 340px;
  pointer-events: none;
}

.toast {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  background: white;
  border: 1px solid #e5e7eb;
  border-left: 3px solid #16a34a;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
  pointer-events: auto;
}

.toast-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #dcfce7;
  color: #15803d;
  font-size: 12px;
  font-weight: 700;
}

.toast-message {
  flex: 1;
  margin: 0;
  font-size: 14px;
  color: #172033;
}

.toast-close {
  flex-shrink: 0;
  border: none;
  background: transparent;
  padding: 0;
  width: 20px;
  height: 20px;
  border-radius: 4px;
  font-size: 18px;
  line-height: 1;
  color: #94a3b8;
  transition: 0.2s ease;
}

.toast-close:hover {
  background: #f1f5f9;
  color: #475569;
}

.toast-enter-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.toast-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
  position: absolute;
  width: 100%;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(20px);
}

.toast-move {
  transition: transform 0.25s ease;
}

@media (max-width: 700px) {
  .toast-stack {
    right: 12px;
    left: 12px;
    bottom: 12px;
    max-width: none;
  }
}
</style>