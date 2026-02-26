import { d as U, r as c, c as $, e as K, q as O, a as n, g as _, b as e, i as o, F as P, t as a, s as q, m as x, v as k, x as L, f as C, p as y, w as N, y as z, u as A, o as l } from './vendor-BOwXO-1K.js'
import { a as G } from './MainHeader.vue_vue_type_script_setup_true_lang-BsVjIPUp.js'
import { u as H } from './useDashboardData-CJiI5ZSQ.js'
import { u as J, b as S } from './main-C9ibHt9P.js'
import { I as W } from './IconSpinner-B_CrXSba.js'
import './axios-B9ygI19o.js'
const Q = { class: 'flex min-h-screen flex-col bg-bgPrimary text-textWhite' },
  X = { class: 'flex flex-1 items-center justify-center px-6 py-10' },
  Y = { class: 'w-full max-w-md space-y-8 rounded-2xl border border-strokePrimary/30 bg-card p-8 shadow-2xl shadow-black/20' },
  Z = { class: 'text-center' },
  ee = { class: 'text-3xl font-bold text-textWhite' },
  te = { class: 'mt-2 text-sm text-textSecondary' },
  se = { class: 'space-y-2' },
  oe = { for: 'email', class: 'text-sm font-semibold text-textWhite' },
  ae = ['placeholder'],
  re = { key: 0, class: 'rounded-lg bg-red-500/10 p-3 text-sm text-red-400' },
  le = ['disabled'],
  ne = { key: 1, class: 'space-y-6' },
  ie = { class: 'rounded-lg bg-green-500/10 p-4 text-sm text-green-400' },
  de = { class: 'text-center text-sm text-textSecondary' },
  ue = { class: 'text-center text-sm text-textSecondary' },
  ce = { key: 0, class: 'text-center py-8' },
  me = { key: 1, class: 'space-y-6' },
  pe = { class: 'rounded-lg bg-red-500/10 p-4 text-sm text-red-400' },
  xe = { class: 'text-center' },
  ve = { class: 'text-3xl font-bold text-textWhite' },
  fe = { class: 'mt-2 text-sm text-textSecondary' },
  ye = { class: 'space-y-4' },
  be = { class: 'space-y-2' },
  ge = { for: 'password', class: 'text-sm font-semibold text-textWhite' },
  he = ['placeholder'],
  _e = { class: 'space-y-2' },
  we = { for: 'password_confirmation', class: 'text-sm font-semibold text-textWhite' },
  Pe = ['placeholder'],
  ke = { key: 0, class: 'rounded-lg bg-red-500/10 p-3 text-sm text-red-400' },
  Le = ['disabled'],
  Se = { key: 1, class: 'space-y-6' },
  We = { class: 'rounded-lg bg-green-500/10 p-4 text-sm text-green-400' },
  Be = { key: 2, class: 'text-center' },
  Me = U({
    __name: 'PasswordResetPage',
    setup(Ve) {
      const B = A(),
        T = O(),
        { dashboardData: j } = H(),
        { t } = J(),
        d = c(!1),
        w = c(!1),
        u = c(''),
        s = c(''),
        b = c(''),
        m = c(''),
        v = c(''),
        p = c(''),
        V = c(''),
        g = c(!1),
        E = j.value?.navLinks ?? [],
        M = $(() => !!p.value)
      K(async () => {
        if (((p.value = T.params.token || ''), p.value)) {
          w.value = !0
          try {
            const r = await S.get(`/api/auth/reset-token/${p.value}`)
            ;((V.value = r.data.email), (g.value = !0))
          } catch (r) {
            ;(r.response?.data?.message ? (s.value = r.response.data.message) : (s.value = t('resetPassword.invalidLink')), (g.value = !1))
          } finally {
            w.value = !1
          }
        }
      })
      const R = async () => {
          if (!b.value) {
            s.value = t('forgotPassword.errorEmptyEmail')
            return
          }
          ;((d.value = !0), (s.value = ''), (u.value = ''))
          try {
            const r = await S.post('/api/auth/forgot-password', { email: b.value })
            u.value = r.data.message
          } catch (r) {
            r.response?.data?.message ? (s.value = r.response.data.message) : (s.value = t('common.error'))
          } finally {
            d.value = !1
          }
        },
        D = async () => {
          if (!m.value || !v.value) {
            s.value = t('resetPassword.errorEmptyFields')
            return
          }
          if (m.value !== v.value) {
            s.value = t('resetPassword.errorPasswordMismatch')
            return
          }
          if (m.value.length < 8) {
            s.value = t('resetPassword.errorPasswordTooShort')
            return
          }
          ;((d.value = !0), (s.value = ''), (u.value = ''))
          try {
            const r = await S.post('/api/auth/reset-password', { token: p.value, password: m.value, password_confirmation: v.value })
            u.value = r.data.message
          } catch (r) {
            if (r.response?.data?.message) s.value = r.response.data.message
            else if (r.response?.data?.errors) {
              const i = r.response.data.errors,
                h = Object.keys(i)[0]
              s.value = i[h][0]
            } else s.value = t('common.error')
          } finally {
            d.value = !1
          }
        },
        F = () => {
          B.push({ name: 'login' })
        },
        I = () => {
          ;((p.value = ''), (V.value = ''), (u.value = ''), (s.value = ''), B.push({ name: 'reset-password' }))
        }
      return (r, i) => {
        const h = z('RouterLink')
        return (
          l(),
          n('div', Q, [
            _(G, { 'nav-links': o(E) }, null, 8, ['nav-links']),
            e('main', X, [
              e('div', Y, [
                M.value
                  ? (l(),
                    n(
                      P,
                      { key: 1 },
                      [
                        w.value
                          ? (l(), n('div', ce, [_(W, { class: 'h-8 w-8 animate-spin text-textPrimary mx-auto' }), i[3] || (i[3] = e('p', { class: 'mt-4 text-sm text-textSecondary' }, 'Weryfikacja linku...', -1))]))
                          : !g.value && s.value
                            ? (l(), n('div', me, [e('div', pe, a(s.value), 1), _(h, { to: { name: 'reset-password' }, class: 'block text-center font-semibold text-textPrimary hover:underline' }, { default: N(() => [y(a(o(t)('resetPassword.requestNewLink')), 1)]), _: 1 })]))
                            : g.value
                              ? (l(),
                                n(
                                  P,
                                  { key: 2 },
                                  [
                                    e('div', xe, [e('h1', ve, a(o(t)('resetPassword.title')), 1), e('p', fe, a(o(t)('resetPassword.subtitle')), 1)]),
                                    u.value
                                      ? (l(), n('div', Se, [e('div', We, a(u.value), 1), e('button', { class: 'flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90', onClick: F }, a(o(t)('resetPassword.goToLogin')), 1)]))
                                      : (l(),
                                        n(
                                          'form',
                                          { key: 0, class: 'space-y-6', onSubmit: q(D, ['prevent']) },
                                          [
                                            e('div', ye, [
                                              e('div', be, [
                                                e('label', ge, a(o(t)('resetPassword.passwordLabel')), 1),
                                                k(
                                                  e(
                                                    'input',
                                                    {
                                                      id: 'password',
                                                      'onUpdate:modelValue': i[1] || (i[1] = (f) => (m.value = f)),
                                                      type: 'password',
                                                      required: '',
                                                      class: 'w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary',
                                                      placeholder: o(t)('resetPassword.passwordPlaceholder'),
                                                    },
                                                    null,
                                                    8,
                                                    he,
                                                  ),
                                                  [[L, m.value]],
                                                ),
                                              ]),
                                              e('div', _e, [
                                                e('label', we, a(o(t)('resetPassword.confirmPasswordLabel')), 1),
                                                k(
                                                  e(
                                                    'input',
                                                    {
                                                      id: 'password_confirmation',
                                                      'onUpdate:modelValue': i[2] || (i[2] = (f) => (v.value = f)),
                                                      type: 'password',
                                                      required: '',
                                                      class: 'w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary',
                                                      placeholder: o(t)('resetPassword.confirmPasswordPlaceholder'),
                                                    },
                                                    null,
                                                    8,
                                                    Pe,
                                                  ),
                                                  [[L, v.value]],
                                                ),
                                              ]),
                                            ]),
                                            s.value ? (l(), n('div', ke, a(s.value), 1)) : x('', !0),
                                            e(
                                              'button',
                                              { type: 'submit', class: 'flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary disabled:cursor-not-allowed disabled:opacity-70', disabled: d.value },
                                              [d.value ? (l(), C(W, { key: 0, class: 'h-5 w-5 animate-spin text-bgPrimary' })) : x('', !0), y(' ' + a(d.value ? o(t)('resetPassword.submitting') : o(t)('resetPassword.submitButton')), 1)],
                                              8,
                                              Le,
                                            ),
                                          ],
                                          32,
                                        )),
                                    s.value && !u.value ? (l(), n('div', Be, [e('button', { class: 'font-semibold text-textPrimary hover:underline text-sm', onClick: I }, a(o(t)('resetPassword.requestNewLink')), 1)])) : x('', !0),
                                  ],
                                  64,
                                ))
                              : x('', !0),
                      ],
                      64,
                    ))
                  : (l(),
                    n(
                      P,
                      { key: 0 },
                      [
                        e('div', Z, [e('h1', ee, a(o(t)('forgotPassword.title')), 1), e('p', te, a(o(t)('forgotPassword.subtitle')), 1)]),
                        u.value
                          ? (l(), n('div', ne, [e('div', ie, a(u.value), 1), e('p', de, a(o(t)('forgotPassword.checkEmail')), 1)]))
                          : (l(),
                            n(
                              'form',
                              { key: 0, class: 'space-y-6', onSubmit: q(R, ['prevent']) },
                              [
                                e('div', se, [
                                  e('label', oe, a(o(t)('forgotPassword.emailLabel')), 1),
                                  k(
                                    e(
                                      'input',
                                      {
                                        id: 'email',
                                        'onUpdate:modelValue': i[0] || (i[0] = (f) => (b.value = f)),
                                        type: 'email',
                                        required: '',
                                        class: 'w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary',
                                        placeholder: o(t)('forgotPassword.emailPlaceholder'),
                                      },
                                      null,
                                      8,
                                      ae,
                                    ),
                                    [[L, b.value]],
                                  ),
                                ]),
                                s.value ? (l(), n('div', re, a(s.value), 1)) : x('', !0),
                                e(
                                  'button',
                                  { type: 'submit', class: 'flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary disabled:cursor-not-allowed disabled:opacity-70', disabled: d.value },
                                  [d.value ? (l(), C(W, { key: 0, class: 'h-5 w-5 animate-spin text-bgPrimary' })) : x('', !0), y(' ' + a(d.value ? o(t)('forgotPassword.submitting') : o(t)('forgotPassword.submitButton')), 1)],
                                  8,
                                  le,
                                ),
                              ],
                              32,
                            )),
                        e('div', ue, [y(a(o(t)('forgotPassword.rememberPassword')) + ' ', 1), _(h, { to: { name: 'login' }, class: 'font-semibold text-textPrimary hover:underline' }, { default: N(() => [y(a(o(t)('forgotPassword.loginLink')), 1)]), _: 1 })]),
                      ],
                      64,
                    )),
              ]),
            ]),
          ])
        )
      }
    },
  })
export { Me as default }
