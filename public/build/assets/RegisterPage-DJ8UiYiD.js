import { d as j, r as l, e as R, a as h, g, b as e, i as s, F as q, t as r, s as N, m as k, v as y, x as S, z as L, f as u, p as b, w as V, y as U, o as i } from './vendor-BOwXO-1K.js'
import { a as A } from './MainHeader.vue_vue_type_script_setup_true_lang-BsVjIPUp.js'
import { a as F, u as $ } from './main-C9ibHt9P.js'
import { u as z } from './useDashboardData-CJiI5ZSQ.js'
import { u as O } from './useRecaptcha-CYKpWRwG.js'
import { I as G } from './IconSpinner-B_CrXSba.js'
import { I as W, a as B } from './IconEyeOff-BqI8ja8j.js'
import './axios-B9ygI19o.js'
const H = { class: 'flex min-h-screen flex-col bg-bgPrimary text-textWhite' },
  J = { class: 'flex flex-1 items-center justify-center px-6 py-10' },
  K = { class: 'w-full max-w-md space-y-8 rounded-2xl border border-strokePrimary/30 bg-card p-8 shadow-2xl shadow-black/20' },
  Q = { class: 'text-center' },
  X = { class: 'text-3xl font-bold text-textWhite' },
  Y = { class: 'mt-2 text-sm text-textSecondary' },
  Z = { class: 'space-y-2' },
  ee = { for: 'name', class: 'text-sm font-semibold text-textWhite' },
  te = ['placeholder'],
  se = { class: 'space-y-2' },
  oe = { for: 'email', class: 'text-sm font-semibold text-textWhite' },
  re = ['placeholder'],
  ae = { class: 'space-y-2' },
  le = { for: 'password', class: 'text-sm font-semibold text-textWhite' },
  ie = { class: 'relative' },
  ne = ['type', 'placeholder'],
  de = { class: 'space-y-2' },
  ce = { for: 'confirmPassword', class: 'text-sm font-semibold text-textWhite' },
  ue = { class: 'relative' },
  me = ['type', 'placeholder'],
  xe = { key: 0, class: 'rounded-lg bg-red-500/10 p-3 text-sm text-red-400' },
  pe = ['disabled'],
  fe = { class: 'text-center text-sm text-textSecondary' },
  ve = { key: 1, class: 'text-center space-y-6' },
  he = { class: 'text-2xl font-bold tracking-tight text-white mb-2' },
  ye = { class: 'text-textSecondary' },
  ge = { class: 'pt-4 border-t border-strokePrimary/30' },
  Be = j({
    __name: 'RegisterPage',
    setup(be) {
      const { register: C } = F(),
        { dashboardData: M } = z(),
        { t } = $(),
        { getToken: D, loadScript: E } = O(),
        m = l(''),
        x = l(''),
        d = l(''),
        p = l(''),
        f = l(!1),
        v = l(!1),
        c = l(!1),
        n = l(''),
        _ = l(!1),
        I = M.value?.navLinks ?? []
      R(() => {
        E()
      })
      const T = async () => {
        if (!m.value || !x.value || !d.value || !p.value) {
          n.value = t('register.errorEmptyFields')
          return
        }
        if (d.value !== p.value) {
          n.value = t('register.errorPasswordMismatch')
          return
        }
        if (d.value.length < 8) {
          n.value = t('register.errorPasswordLength')
          return
        }
        ;((c.value = !0), (n.value = ''))
        try {
          const w = await D('register')
          ;(await C({ name: m.value, email: x.value, password: d.value, recaptcha_token: w }), (_.value = !0))
        } catch {
          n.value = t('common.error')
        } finally {
          c.value = !1
        }
      }
      return (w, o) => {
        const P = U('RouterLink')
        return (
          i(),
          h('div', H, [
            g(A, { 'nav-links': s(I) }, null, 8, ['nav-links']),
            e('main', J, [
              e('div', K, [
                _.value
                  ? (i(),
                    h('div', ve, [
                      o[6] ||
                        (o[6] = e(
                          'div',
                          { class: 'mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-500/20 text-green-500' },
                          [
                            e('svg', { xmlns: 'http://www.w3.org/2000/svg', fill: 'none', viewBox: '0 0 24 24', 'stroke-width': '2', stroke: 'currentColor', class: 'h-8 w-8' }, [
                              e('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75' }),
                            ]),
                          ],
                          -1,
                        )),
                      e('div', null, [e('h2', he, r(s(t)('register.verifyEmailTitle')), 1), e('p', ye, r(s(t)('register.verifyEmailMessage')), 1)]),
                      e('div', ge, [g(P, { to: { name: 'login' }, class: 'flex w-full items-center justify-center rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90' }, { default: V(() => [b(r(s(t)('register.goToLogin')), 1)]), _: 1 })]),
                    ]))
                  : (i(),
                    h(
                      q,
                      { key: 0 },
                      [
                        e('div', Q, [e('h1', X, r(s(t)('register.title')), 1), e('p', Y, r(s(t)('register.subtitle')), 1)]),
                        e(
                          'form',
                          { class: 'space-y-5', onSubmit: N(T, ['prevent']) },
                          [
                            e('div', Z, [
                              e('label', ee, r(s(t)('register.nameLabel')), 1),
                              y(
                                e(
                                  'input',
                                  { id: 'name', 'onUpdate:modelValue': o[0] || (o[0] = (a) => (m.value = a)), type: 'text', required: '', class: 'w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary', placeholder: s(t)('register.namePlaceholder') },
                                  null,
                                  8,
                                  te,
                                ),
                                [[S, m.value]],
                              ),
                            ]),
                            e('div', se, [
                              e('label', oe, r(s(t)('register.emailLabel')), 1),
                              y(
                                e(
                                  'input',
                                  {
                                    id: 'email',
                                    'onUpdate:modelValue': o[1] || (o[1] = (a) => (x.value = a)),
                                    type: 'email',
                                    required: '',
                                    class: 'w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary',
                                    placeholder: s(t)('register.emailPlaceholder'),
                                  },
                                  null,
                                  8,
                                  re,
                                ),
                                [[S, x.value]],
                              ),
                            ]),
                            e('div', ae, [
                              e('label', le, r(s(t)('register.passwordLabel')), 1),
                              e('div', ie, [
                                y(
                                  e(
                                    'input',
                                    {
                                      id: 'password',
                                      'onUpdate:modelValue': o[2] || (o[2] = (a) => (d.value = a)),
                                      type: f.value ? 'text' : 'password',
                                      required: '',
                                      minlength: '8',
                                      class: 'w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 pr-12 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary',
                                      placeholder: s(t)('register.passwordPlaceholder'),
                                    },
                                    null,
                                    8,
                                    ne,
                                  ),
                                  [[L, d.value]],
                                ),
                                e('button', { type: 'button', class: 'absolute right-3 top-1/2 -translate-y-1/2 text-textSecondary hover:text-textWhite focus:outline-none', onClick: o[3] || (o[3] = (a) => (f.value = !f.value)) }, [f.value ? (i(), u(W, { key: 0, class: 'h-5 w-5' })) : (i(), u(B, { key: 1, class: 'h-5 w-5' }))]),
                              ]),
                            ]),
                            e('div', de, [
                              e('label', ce, r(s(t)('register.confirmPasswordLabel')), 1),
                              e('div', ue, [
                                y(
                                  e(
                                    'input',
                                    {
                                      id: 'confirmPassword',
                                      'onUpdate:modelValue': o[4] || (o[4] = (a) => (p.value = a)),
                                      type: v.value ? 'text' : 'password',
                                      required: '',
                                      class: 'w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 pr-12 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary',
                                      placeholder: s(t)('register.confirmPasswordPlaceholder'),
                                    },
                                    null,
                                    8,
                                    me,
                                  ),
                                  [[L, p.value]],
                                ),
                                e('button', { type: 'button', class: 'absolute right-3 top-1/2 -translate-y-1/2 text-textSecondary hover:text-textWhite focus:outline-none', onClick: o[5] || (o[5] = (a) => (v.value = !v.value)) }, [v.value ? (i(), u(W, { key: 0, class: 'h-5 w-5' })) : (i(), u(B, { key: 1, class: 'h-5 w-5' }))]),
                              ]),
                            ]),
                            n.value ? (i(), h('div', xe, r(n.value), 1)) : k('', !0),
                            e(
                              'button',
                              { type: 'submit', class: 'flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary disabled:cursor-not-allowed disabled:opacity-70', disabled: c.value },
                              [c.value ? (i(), u(G, { key: 0, class: 'h-5 w-5 animate-spin text-bgPrimary' })) : k('', !0), e('span', null, r(c.value ? s(t)('register.submitting') : s(t)('register.submitButton')), 1)],
                              8,
                              pe,
                            ),
                          ],
                          32,
                        ),
                        e('div', fe, [b(r(s(t)('register.hasAccount')) + ' ', 1), g(P, { to: { name: 'login' }, class: 'font-semibold text-textPrimary hover:underline' }, { default: V(() => [b(r(s(t)('register.loginLink')), 1)]), _: 1 })]),
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
export { Be as default }
