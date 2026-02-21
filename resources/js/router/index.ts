import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const MainPage = () => import('@/pages/MainPage.vue')
const LoginPage = () => import('@/pages/LoginPage.vue')
const RegisterPage = () => import('@/pages/RegisterPage.vue')
const ProfileSite = () => import('@/pages/ProfileSite.vue')
const ProfileSettingsPage = () => import('@/pages/ProfileSettingsPage.vue')
const CoursePage = () => import('@/pages/CoursePage.vue')
const LessonPage = () => import('@/pages/LessonPage.vue')
const NewsPage = () => import('@/pages/NewsPage.vue')
const ArticlePage = () => import('@/pages/ArticlePage.vue')
const AdminLayout = () => import('@/layouts/AdminLayout.vue')
const AdminUsersPage = () => import('@/pages/admin/AdminUsersPage.vue')
const AdminLogsPage = () => import('@/pages/admin/AdminLogsPage.vue')

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'main',
      component: MainPage,
    },
    {
      path: '/logowanie',
      name: 'login',
      component: LoginPage,
    },
    {
      path: '/rejestracja',
      name: 'register',
      component: RegisterPage,
    },
    {
      path: '/nie-pamietam-hasla',
      name: 'forgot-password',
      redirect: '/reset-hasla',
    },
    {
      path: '/reset-hasla/:token?',
      name: 'reset-password',
      component: () => import('@/pages/PasswordResetPage.vue'),
    },
    {
      path: '/profil',
      name: 'profile',
      component: ProfileSite,
      meta: { requiresAuth: true },
    },
    {
      path: '/ustawienia-profilu',
      name: 'profile-settings',
      component: ProfileSettingsPage,
      meta: { requiresAuth: true },
    },
    {
      path: '/aktualnosci',
      name: 'news',
      component: NewsPage,
    },
    {
      path: '/artykul/:articleId',
      name: 'article-detail',
      component: ArticlePage,
    },
    {
      path: '/kurs/:courseId',
      name: 'course-detail',
      component: CoursePage,
    },
    {
      path: '/kurs/:courseId/lekcja/:lessonOrder',
      name: 'lesson',
      component: LessonPage,
      props: true,
      meta: { requiresAuth: false },
    },
    {
      path: '/admin',
      component: AdminLayout,
      meta: { requiresAuth: true, requiresAdmin: true },
      children: [
        {
          path: '',
          redirect: '/admin/users',
        },
        {
          path: 'users',
          name: 'admin-users',
          component: AdminUsersPage,
        },
        {
          path: 'logs',
          name: 'admin-logs',
          component: AdminLogsPage,
        },
        {
          path: 'courses',
          name: 'AdminCourses',
          component: () => import('@/pages/admin/AdminCoursesPage.vue'),
        },
        {
          path: 'courses/:id',
          name: 'AdminCourseEdit',
          component: () => import('@/pages/admin/AdminCourseEditPage.vue'),
        },

        {
          path: 'news',
          name: 'AdminNews',
          component: () => import('@/pages/admin/AdminNewsPage.vue'),
        },
        {
          path: 'news/:id',
          name: 'AdminNewsEdit',
          component: () => import('@/pages/admin/AdminNewsEditPage.vue'),
        },
      ],
    },
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0, behavior: 'auto' }
    }
  },
})

router.beforeEach(async (to, from, next) => {
  const { isLoggedIn, isLoading, initAuth } = useAuth()

  if (isLoading.value) {
    await initAuth()
  }

  if (to.meta.requiresAuth && !isLoggedIn.value) {
    next({ name: 'login', query: { redirect: to.fullPath } })
  } else if (to.meta.requiresAdmin) {
    const { user } = useAuth()
    if (user.value?.role !== 'admin') {
      next({ name: 'main' })
    } else {
      next()
    }
  } else {
    next()
  }
})
