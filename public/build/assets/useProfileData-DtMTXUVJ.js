import { u as A, a as f, b as v } from './main-C9ibHt9P.js'
import { a as I, c as $, b } from './useDashboardData-CJiI5ZSQ.js'
import { B as D } from './vendor-BOwXO-1K.js'
let d = null
function S() {
  const { currentLanguage: p } = A(),
    { refreshUser: i } = f(),
    u = new Set(),
    {
      data: e,
      isLoading: m,
      error: g,
      load: h,
      refresh: y,
    } = I(async () => {
      const { isLoggedIn: s } = f()
      return s.value ? b(p.value) : null
    }),
    L = (s = !1) => h(s),
    n = () => y(),
    P = async (s, a, t, r) => {
      if (!e.value) return
      const c = `${s}-${a}`
      if (u.has(c)) return
      u.add(c)
      const l = e.value.courseProgress.find((o) => o.courseId === s)
      l && (l.completedLessonIds.includes(a) || l.completedLessonIds.push(a), r && (l.savedCode = { ...l.savedCode, [a]: r }))
      try {
        const o = await $(s, a, r),
          { setUser: C } = f()
        ;(o && o.user ? C(o.user) : await i(), await n())
      } catch (o) {
        console.error('Failed to save progress', o)
      } finally {
        u.delete(c)
      }
    },
    w = async (s, a) => {
      if (e.value) {
        const t = e.value.courseProgress.find((r) => r.courseId === s)
        t
          ? (t.status = 'enrolled')
          : (e.value.courseProgress.push({ courseId: s, completedLessonIds: [], savedCode: {}, status: 'enrolled' }),
            e.value.summary.inProgress++,
            a && e.value.enrolledCourses.push({ id: s, title: a.title, level: a.level || 'Junior', image_path: a.image_path, progress: 0, completedLessons: 0, totalLessons: a.totalLessons || 0, accent: 'blue', status: 'enrolled', updatedAt: new Date().toISOString(), nextLesson: void 0 }))
      }
      try {
        await v.post(`/api/courses/${s}/enroll`)
      } finally {
        ;(n(), i())
      }
    },
    x = async (s) => {
      if (e.value) {
        const a = e.value.courseProgress.findIndex((r) => r.courseId === s)
        a !== -1 && (e.value.courseProgress.splice(a, 1), (e.value.summary.inProgress = Math.max(0, e.value.summary.inProgress - 1)))
        const t = e.value.enrolledCourses.findIndex((r) => r.id === s)
        t !== -1 && e.value.enrolledCourses.splice(t, 1)
      }
      try {
        await v.delete(`/api/courses/${s}/enroll`)
      } finally {
        ;(n(), i())
      }
    }
  return (
    D(p, () => {
      e.value && n()
    }),
    { profileData: e, isProfileLoading: m, profileError: g, loadProfileData: L, refreshProfileData: n, markLessonAsCompleted: P, enrollInCourse: w, leaveCourse: x }
  )
}
const B = () => (d || (d = S()), d)
export { B as u }
