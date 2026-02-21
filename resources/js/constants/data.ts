export type NavLink = {
  id: 'home' | 'profile' | 'news'
  label: string
  path: string
}

export type FilterOption = {
  id: string
  label: string
}

export type FilterSection = {
  id: string
  title: string
  options: FilterOption[]
}

export type Lesson = {
  id: string
  title: string
  duration: string
  isCompleted: boolean
  isLocked: boolean
  taskContent?: string
  initialCode?: string
  validationRegex?: string
  judge0LanguageId?: number
  expectedOutput?: string
  testCode?: string
  editorLabel?: string
  hint?: string
  hint2?: string
  previewType?: 'css' | 'js' | 'none'
  order?: number
  content?: string
  isActive?: boolean
  exerciseId?: string
}

export type CourseCard = {
  id: string
  title: string
  subtitle: string
  level: string
  reward: string
  tasks: string
  duration: string
  accent: string
  description: string
  lessons?: Lesson[]
  tags?: string[]
  enrolledCount?: number
  image_path?: string
}

export type ProfileSummary = {
  nickname: string
  description: string
  avatarUrl?: string
  progress: number
  completed: number
  inProgress: number
  waitingTasks: number
  level?: string
  xp?: number
}

export type UserStat = {
  id: string
  label: string
  value: string
  helper: string
}

export type Achievement = {
  id: string
  title: string
  title_en?: string
  type: string
  level: string
  isEarned?: boolean
  description: string
  description_en?: string
}

export type ProfileCourse = {
  id: string
  title: string
  level: string
  progress: number
  completedLessons: number
  totalLessons: number
  accent: string
  status: 'completed' | 'enrolled'
  updatedAt: string
  nextLesson?: string
  image_path?: string
}

export const navLinks: NavLink[] = [
  { id: 'home', label: 'Strona główna', path: '/' },
  { id: 'news', label: 'Aktualności', path: '/aktualnosci' },
  { id: 'profile', label: 'Profil', path: '/profil' },
]

export const filterSections: FilterSection[] = [
  {
    id: 'levels',
    title: 'filters.levels',
    options: [
      { id: 'junior', label: 'Junior' },
      { id: 'mid', label: 'Mid' },
      { id: 'senior', label: 'Senior' },
    ],
  },
  {
    id: 'tech',
    title: 'filters.technologies',
    options: [
      { id: 'javascript', label: 'JavaScript' },
      { id: 'php', label: 'PHP' },
      { id: 'react', label: 'React' },
      { id: 'vue', label: 'Vue' },
      { id: 'node.js', label: 'Node.js' },
      { id: 'laravel', label: 'Laravel' },
    ],
  },
]

export type NewsArticle = {
  id: string
  title: string
  excerpt: string
  date: string
  readTime: string
  author: string
  authorAvatar?: string
  authorEmail?: string
  category: string
  tags: string[]
  accent: string
  content?: string
  is_published?: boolean
}
