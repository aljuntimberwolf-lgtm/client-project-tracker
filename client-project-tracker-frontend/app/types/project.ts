export interface Project {
  id: number
  client_name: string
  project_name: string
  description: string | null
  status: string
  priority: string
  start_date: string
  due_date: string
}

export interface ProjectFormData {
  client_name: string
  project_name: string
  description: string
  status: string
  priority: string
  start_date: string
  due_date: string
}