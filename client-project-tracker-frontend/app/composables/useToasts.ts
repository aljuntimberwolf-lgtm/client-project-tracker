import type { Toast } from '~/types/toast'

export function useToasts(duration = 3500) {
  const toasts = ref<Toast[]>([])
  let nextId = 0

  function dismissToast(id: number) {
    toasts.value = toasts.value.filter((toast) => toast.id !== id)
  }

  function showToast(message: string) {
    const id = ++nextId

    toasts.value.push({ id, message })

    setTimeout(() => dismissToast(id), duration)
  }

  return { toasts, showToast, dismissToast }
}