export function useModalDismiss(onEscape: () => void) {
  let previousOverflow = ''

  function handleKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') {
      onEscape()
    }
  }

  onMounted(() => {
    previousOverflow = document.body.style.overflow
    document.body.style.overflow = 'hidden'
    document.addEventListener('keydown', handleKeydown)
  })

  onBeforeUnmount(() => {
    document.body.style.overflow = previousOverflow
    document.removeEventListener('keydown', handleKeydown)
  })
}