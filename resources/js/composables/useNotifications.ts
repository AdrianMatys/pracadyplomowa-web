import { ref } from 'vue'

export type NotificationType = 'success' | 'error' | 'info'

export interface Notification {
  id: number
  type: NotificationType
  message: string
}

const notifications = ref<Notification[]>([])
let nextId = 0

export const useNotifications = () => {
  const remove = (id: number) => {
    const index = notifications.value.findIndex((n) => n.id === id)
    if (index !== -1) {
      notifications.value.splice(index, 1)
    }
  }

  const notify = (type: NotificationType, message: string, duration = 5000) => {
    const id = nextId++
    notifications.value.push({ id, type, message })

    if (duration > 0) {
      setTimeout(() => {
        remove(id)
      }, duration)
    }
  }

  return {
    notifications,
    notify,
    remove,
  }
}
