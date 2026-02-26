import api from '@/services/api'
import { formatAvatarUrl } from '@/utils/formatters'
import type { CourseCard, FilterSection, NavLink, ProfileSummary, ProfileCourse, NewsArticle } from '@/constants/data'
import { navLinks, filterSections } from '@/constants/data'

export type DashboardResponse = {
  courses: CourseCard[]
  filterSections: FilterSection[]
  navLinks: NavLink[]
  stats: {
    coursesCount: number
    tasksCount: number
    totalPoints: number
  }
}

const mapCourse = (course: any): CourseCard => ({
  id: String(course.id),
  title: course.title,
  subtitle: course.description,
  level: course.level || 'Junior',
  reward: String(course.reward || 0),
  tasks: String(course.lessons_count || course.lessons?.length || 0),
  duration: course.duration ? `${course.duration} min` : '30 min',
  accent: 'blue',
  description: course.description,
  lessons: (course.lessons || []).map((lesson: any) => {
    const exercise = lesson.exercises?.[0] || lesson.exercise
    return {
      id: String(lesson.id),
      title: lesson.title,
      duration: '10 min',
      isCompleted: false,
      isLocked: false,
      order: lesson.order,
      content: lesson.content,
      taskContent: exercise?.description || lesson.content,
      initialCode: exercise?.initial_code || '',
      validationRegex: exercise?.validation_regex,
      judge0LanguageId: exercise?.judge0_language_id,
      expectedOutput: exercise?.expected_output,
      testCode: exercise?.test_code,
      editorLabel: exercise?.editor_label,
      exerciseId: exercise?.id ? String(exercise.id) : undefined,
      hint: exercise?.hint,
      hint2: exercise?.hint_2,
      previewType: exercise?.preview_type || 'none',
    }
  }),
  tags: course.tags?.map((t: any) => t.name || t) || [],
  enrolledCount: course.progress_count || course.enrolled_count || 0,
  image_path: course.image_path ? (course.image_path.startsWith('/') || course.image_path.startsWith('http') ? course.image_path : '/' + course.image_path) : undefined,
})

export const fetchDashboardData = async (language: string): Promise<DashboardResponse> => {
  const [coursesResponse, technologiesResponse] = await Promise.all([
    api.get('/api/courses', {
      headers: { 'Accept-Language': language },
    }),
    api.get('/api/technologies', {
      headers: { 'Accept-Language': language },
    }),
  ])

  const coursesData = coursesResponse.data
  const technologiesData = technologiesResponse.data

  const courses: CourseCard[] = (Array.isArray(coursesData) ? coursesData : coursesData.data || []).map(mapCourse)

  const techOptions = (Array.isArray(technologiesData) ? technologiesData : []).map((t: any) => ({
    id: t.name?.toLowerCase() || String(t.id),
    label: t.name,
  }))

  const dynamicFilterSections: FilterSection[] = [
    filterSections[0],
    {
      id: 'tech',
      title: 'filters.technologies',
      options: techOptions,
    },
  ]

  const totalTasks = courses.reduce((sum, c) => sum + parseInt(c.tasks || '0', 10), 0)
  const totalPoints = courses.reduce((sum, c) => sum + parseInt(c.reward || '0', 10), 0)

  return {
    courses,
    filterSections: dynamicFilterSections,
    navLinks: navLinks,
    stats: {
      coursesCount: courses.length,
      tasksCount: totalTasks,
      totalPoints: totalPoints,
    },
  }
}

export const fetchCourseById = async (id: string, language: string): Promise<CourseCard | null> => {
  try {
    const response = await api.get(`/api/courses/${id}`, {
      headers: { 'Accept-Language': language },
    })
    return mapCourse(response.data)
  } catch (error) {
    console.error('Error fetching course:', error)
    return null
  }
}

export type CourseProgress = {
  courseId: string
  completedLessonIds: string[]
  savedCode: Record<string, string>
  status: 'enrolled' | 'completed'
}

export type ProfileResponse = {
  summary: ProfileSummary
  achievements: any[]
  completedCourses: ProfileCourse[]
  enrolledCourses: ProfileCourse[]
  courseProgress: CourseProgress[]
}

export const fetchProfileData = async (language: string): Promise<ProfileResponse> => {
  const [userResponse, coursesResponse, achievementsResponse] = await Promise.all([
    api.get('/api/users/me', {
      headers: { 'Accept-Language': language },
    }),
    api.get('/api/courses', {
      headers: { 'Accept-Language': language },
    }),
    api.get('/api/users/me/achievements', {
      headers: { 'Accept-Language': language },
    }),
  ])

  const userData = userResponse.data
  const allCourses = Array.isArray(coursesResponse.data) ? coursesResponse.data : coursesResponse.data.data || []
  const allAchievementsData = achievementsResponse.data

  const courseProgress: CourseProgress[] = (userData.progress || []).map((p: any) => ({
    courseId: String(p.course_id),
    completedLessonIds: (p.completed_lesson_ids || []).map(String),
    savedCode: p.saved_code || {},
    status: p.status || 'enrolled',
  }))

  const completedCourses: ProfileCourse[] = []
  const enrolledCourses: ProfileCourse[] = []

  for (const progress of userData.progress || []) {
    const course = progress.course || allCourses.find((c: any) => c.id === progress.course_id)
    if (!course) continue

    const totalLessons = course.lessons_count || (Array.isArray(course.lessons) ? course.lessons.length : 0)
    const completedLessonsCount = (progress.completed_lesson_ids || []).length
    const progressPercent = totalLessons > 0 ? Math.round((completedLessonsCount / totalLessons) * 100) : 0
    const nextLessonTitle = Array.isArray(course.lessons) ? course.lessons.find((l: any) => !(progress.completed_lesson_ids || []).includes(String(l.id)))?.title : undefined

    const profileCourse: ProfileCourse = {
      id: String(course.id),
      title: course.title,
      level: course.level || 'Junior',
      progress: progressPercent,
      completedLessons: completedLessonsCount,
      totalLessons: totalLessons,
      accent: 'blue',
      status: progress.status === 'completed' ? 'completed' : 'enrolled',
      updatedAt: progress.updated_at || new Date().toISOString(),
      nextLesson: nextLessonTitle,
      image_path: course.image_path ? (course.image_path.startsWith('/') || course.image_path.startsWith('http') ? course.image_path : '/' + course.image_path) : undefined,
    }

    if (progress.status === 'completed') {
      completedCourses.push(profileCourse)
    } else {
      enrolledCourses.push(profileCourse)
    }
  }

  const xp = userData.profile?.experience_points || 0
  const summary: ProfileSummary = {
    nickname: userData.profile?.nickname || userData.email || 'User',
    description: userData.profile?.bio || '',
    avatarUrl: formatAvatarUrl(userData.profile?.avatar_url),
    progress: 0,
    completed: completedCourses.length,
    inProgress: enrolledCourses.length,
    waitingTasks: userData.profile?.streak || 0,
    level: Math.floor(xp / 1000) + 1 <= 5 ? 'Junior' : Math.floor(xp / 1000) + 1 <= 15 ? 'Mid' : 'Senior',
    xp: xp,
  }

  const achievements = (allAchievementsData || []).map((a: any) => ({
    id: String(a.id),
    title: a.title || a.name,
    title_en: a.title_en,
    type: a.type || 'achievement',
    level: a.level || 'bronze',
    isEarned: !!a.isEarned,
    description: a.description || '',
    description_en: a.description_en,
  }))

  return {
    summary,
    achievements,
    completedCourses,
    enrolledCourses,
    courseProgress,
  }
}

export const completeLesson = async (courseId: string, lessonId: string, userCode?: string): Promise<any> => {
  const response = await api.post(`/api/courses/${courseId}/lessons/${lessonId}/complete`, {
    user_code: userCode,
  })
  return response.data
}

export type NewsResponse = {
  articles: NewsArticle[]
  featured?: NewsArticle
}

const mapArticle = (article: any): NewsArticle => {
  const authorData = article.user || article.author
  return {
    id: String(article.id),
    title: article.title,
    excerpt: article.excerpt || article.content?.substring(0, 150) + '...',
    content: article.content,
    date: article.created_at || article.date || article.updated_at,
    readTime: article.read_time || '5 min',
    author: authorData?.profile?.nickname || authorData?.email || article.author_name || 'Admin',
    authorAvatar: formatAvatarUrl(authorData?.profile?.avatar_url),
    authorEmail: authorData?.email,
    category: article.category || article.type || 'news',
    tags: article.tags?.map((t: any) => t.name || t) || [],
    accent: article.accent || 'blue',
    is_published: article.is_published,
  }
}

export const fetchNewsData = async (): Promise<NewsResponse> => {
  const response = await api.get('/api/news')
  const data = response.data
  const articles = (data.data || data || []).map(mapArticle)
  return { articles, featured: articles[0] }
}

export const fetchArticleById = async (id: string): Promise<NewsArticle | null> => {
  try {
    const response = await api.get(`/api/news/${id}`)
    return mapArticle(response.data)
  } catch {
    return null
  }
}
