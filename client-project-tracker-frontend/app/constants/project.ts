import type { Project } from '~/types/project'

export const STATUS_OPTIONS = [
  'Planning',
  'In Progress',
  'On Hold',
  'Completed'
]

export const PRIORITY_OPTIONS = ['Low', 'Medium', 'High']

export const PRIORITY_ORDER: Record<string, number> = {
  Low: 1,
  Medium: 2,
  High: 3
}

export const STATUS_CLASSES: Record<string, string> = {
  Planning: 'status-planning',
  'In Progress': 'status-progress',
  'On Hold': 'status-hold',
  Completed: 'status-completed'
}

export const PRIORITY_CLASSES: Record<string, string> = {
  Low: 'priority-low',
  Medium: 'priority-medium',
  High: 'priority-high'
}

export const SORT_OPTIONS = [
  { value: '', label: 'Default' },
  { value: 'client_asc', label: 'Client Name A → Z' },
  { value: 'client_desc', label: 'Client Name Z → A' },
  { value: 'project_asc', label: 'Project Name A → Z' },
  { value: 'project_desc', label: 'Project Name Z → A' },
  { value: 'start_asc', label: 'Start Date — Oldest' },
  { value: 'start_desc', label: 'Start Date — Newest' },
  { value: 'due_asc', label: 'Due Date — Earliest' },
  { value: 'due_desc', label: 'Due Date — Latest' },
  { value: 'priority_asc', label: 'Priority — Low to High' },
  { value: 'priority_desc', label: 'Priority — High to Low' }
]

export const SORT_COMPARATORS: Record<
  string,
  (a: Project, b: Project) => number
> = {
  client_asc: (a, b) => a.client_name.localeCompare(b.client_name),
  client_desc: (a, b) => b.client_name.localeCompare(a.client_name),
  project_asc: (a, b) => a.project_name.localeCompare(b.project_name),
  project_desc: (a, b) => b.project_name.localeCompare(a.project_name),
  start_asc: (a, b) => a.start_date.localeCompare(b.start_date),
  start_desc: (a, b) => b.start_date.localeCompare(a.start_date),
  due_asc: (a, b) => a.due_date.localeCompare(b.due_date),
  due_desc: (a, b) => b.due_date.localeCompare(a.due_date),
  priority_asc: (a, b) =>
    (PRIORITY_ORDER[a.priority] ?? 0) - (PRIORITY_ORDER[b.priority] ?? 0),
  priority_desc: (a, b) =>
    (PRIORITY_ORDER[b.priority] ?? 0) - (PRIORITY_ORDER[a.priority] ?? 0)
}