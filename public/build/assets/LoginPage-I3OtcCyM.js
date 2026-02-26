import { d as T, r as l, e as q, q as N, a as p, g as w, b as t, i as s, t as o, s as j, m as f, v as L, x as U, w as S, p as x, y as z, z as O, f as h, u as A, o as n } from './vendor-BOwXO-1K.js'
import { a as F } from './MainHeader.vue_vue_type_script_setup_true_lang-BsVjIPUp.js'
import { a as K, u as $, b as G } from './main-C9ibHt9P.js'
import { u as H } from './useDashboardData-CJiI5ZSQ.js'
import { u as J } from './useRecaptcha-CYKpWRwG.js'
import { I as V } from './IconSpinner-B_CrXSba.js'
import { I as Q, a as X } from './IconEyeOff-BqI8ja8j.js'
import './axios-B9ygI19o.js'
const Y = { class: 'flex min-h-screen flex-col bg-bgPrimary text-textWhite' },
  Z = { class: 'flex flex-1 items-center justify-center px-6 py-10' },
  ee = { class: 'w-full max-w-md space-y-8 rounded-2xl border border-strokePrimary/30 bg-card p-8 shadow-2xl shadow-black/20' },
  te = { class: 'text-center' },
  se = { class: 'text-3xl font-bold text-textWhite' },
  oe = { class: 'mt-2 text-sm text-textSecondary' },
  ae = { class: 'space-y-4' },
  re = { class: 'space-y-2' },
  ie = { for: 'email', class: 'text-sm font-semibold text-textWhite' },
  le = ['placeholder'],
  ne = { class: 'space-y-2' },
  de = { class: 'flex items-center justify-between' },
  ce = { for: 'password', class: 'text-sm font-semibold text-textWhite' },
  ue = { class: 'relative' },
  me = ['type', 'placeholder'],
  pe = { key: 0, class: 'rounded-lg bg-emerald-500/10 border border-emerald-500/20 p-4 text-sm space-y-1' },
  fe = { class: 'font-semibold text-emerald-400' },
  xe = { class: 'text-emerald-300/80' },
  ve = { key: 1, class: 'rounded-lg bg-green-500/10 p-3 text-sm text-green-400' },
  ye = { key: 2, class: 'rounded-lg bg-red-500/10 p-3 text-sm text-red-400 space-y-2' },
  ge = ['disabled'],
  be = ['disabled'],
  he = { class: 'text-center text-sm text-textSecondary' },
  Be = T({
    __name: 'LoginPage',
    setup(_e) {
      const E = A(),
        k = N(),
        { login: W } = K(),
        { dashboardData: B } = H(),
        { t: e } = $(),
        { getToken: R, loadScript: C } = J(),
        d = l(''),
        c = l(''),
        v = l(!1),
        u = l(!1),
        r = l(''),
        y = l(!1),
        P = l(!1),
        m = l(!1),
        _ = l(!1),
        D = B.value?.navLinks ?? []
      q(() => {
        ;(C(), k.query.verified === '1' && (P.value = !0))
      })
      const I = async () => {
          if (!d.value || !c.value) {
            r.value = e('login.errorEmptyFields')
            return
          }
          ;((u.value = !0), (r.value = ''), (y.value = !1), (_.value = !1))
          try {
            const i = await R('login')
            await W({ email: d.value, password: c.value, recaptcha_token: i })
            const a = k.query.redirect || '/'
            E.push(a)
          } catch (i) {
            if (i.response?.status === 403 && i.response.data?.email_unverified) ((y.value = !0), (r.value = e('login.emailUnverifiedError')))
            else if (i.response?.status === 422) {
              const a = i.response.data.errors
              if (a) {
                const g = Object.keys(a)[0]
                r.value = a[g][0]
              } else r.value = i.response.data.message || e('login.errorEmptyFields')
            } else i.code === 'ERR_NETWORK' ? (r.value = e('common.connectionError') || 'Błąd połączenia z serwerem') : (r.value = e('common.error') || 'Wystąpił błąd logowania')
          } finally {
            u.value = !1
          }
        },
        M = async () => {
          m.value = !0
          try {
            ;(await G.post('/api/auth/resend-verification', { email: d.value, password: c.value }), (_.value = !0), (r.value = ''), (y.value = !1))
          } catch {
            r.value = e('login.resendError')
          } finally {
            m.value = !1
          }
        }
      return (i, a) => {
        const g = z('RouterLink')
        return (
          n(),
          p('div', Y, [
            w(F, { 'nav-links': s(D) }, null, 8, ['nav-links']),
            t('main', Z, [
              t('div', ee, [
                t('div', te, [t('h1', se, o(s(e)('login.title')), 1), t('p', oe, o(s(e)('login.subtitle')), 1)]),
                t(
                  'form',
                  { class: 'space-y-6', onSubmit: j(I, ['prevent']) },
                  [
                    t('div', ae, [
                      t('div', re, [
                        t('label', ie, o(s(e)('login.emailLabel')), 1),
                        L(
                          t(
                            'input',
                            { id: 'email', 'onUpdate:modelValue': a[0] || (a[0] = (b) => (d.value = b)), type: 'email', required: '', class: 'w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary', placeholder: s(e)('login.emailPlaceholder') },
                            null,
                            8,
                            le,
                          ),
                          [[U, d.value]],
                        ),
                      ]),
                      t('div', ne, [
                        t('div', de, [t('label', ce, o(s(e)('login.passwordLabel')), 1), w(g, { to: { name: 'forgot-password' }, class: 'text-xs text-textPrimary hover:underline' }, { default: S(() => [x(o(s(e)('login.forgotPassword')), 1)]), _: 1 })]),
                        t('div', ue, [
                          L(
                            t(
                              'input',
                              {
                                id: 'password',
                                'onUpdate:modelValue': a[1] || (a[1] = (b) => (c.value = b)),
                                type: v.value ? 'text' : 'password',
                                required: '',
                                class: 'w-full rounded-xl border border-strokePrimary/40 bg-inputBg px-4 py-3 pr-12 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary',
                                placeholder: s(e)('login.passwordPlaceholder'),
                              },
                              null,
                              8,
                              me,
                            ),
                            [[O, c.value]],
                          ),
                          t('button', { type: 'button', class: 'absolute right-3 top-1/2 -translate-y-1/2 text-textSecondary hover:text-textWhite focus:outline-none', onClick: a[2] || (a[2] = (b) => (v.value = !v.value)) }, [v.value ? (n(), h(Q, { key: 0, class: 'h-5 w-5' })) : (n(), h(X, { key: 1, class: 'h-5 w-5' }))]),
                        ]),
                      ]),
                    ]),
                    P.value ? (n(), p('div', pe, [t('p', fe, o(s(e)('register.authVerifiedTitle')), 1), t('p', xe, o(s(e)('register.authVerifiedMessage')), 1)])) : f('', !0),
                    _.value
                      ? (n(), p('div', ve, '✓ ' + o(s(e)('login.resendSuccess')), 1))
                      : r.value
                        ? (n(),
                          p('div', ye, [
                            t('p', null, o(r.value), 1),
                            y.value
                              ? (n(), p('button', { key: 0, type: 'button', class: 'mt-1 flex items-center gap-2 text-xs font-semibold text-amber-400 hover:text-amber-300 transition-colors', disabled: m.value, onClick: M }, [m.value ? (n(), h(V, { key: 0, class: 'h-3 w-3 animate-spin' })) : f('', !0), x(' ' + o(m.value ? s(e)('common.submitting') : s(e)('login.resendLink')), 1)], 8, ge))
                              : f('', !0),
                          ]))
                        : f('', !0),
                    t(
                      'button',
                      { type: 'submit', class: 'flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary disabled:cursor-not-allowed disabled:opacity-70', disabled: u.value },
                      [u.value ? (n(), h(V, { key: 0, class: 'h-5 w-5 animate-spin text-bgPrimary' })) : f('', !0), x(' ' + o(u.value ? s(e)('login.submitting') : s(e)('login.submitButton')), 1)],
                      8,
                      be,
                    ),
                  ],
                  32,
                ),
                t('div', he, [x(o(s(e)('login.noAccount')) + ' ', 1), w(g, { to: { name: 'register' }, class: 'font-semibold text-textPrimary hover:underline' }, { default: S(() => [x(o(s(e)('login.registerLink')), 1)]), _: 1 })]),
              ]),
            ]),
          ])
        )
      }
    },
  })
export { Be as default }
