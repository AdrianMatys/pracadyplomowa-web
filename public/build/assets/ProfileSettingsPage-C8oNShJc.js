import { d as G, c as u, r as n, e as J, B as K, a as v, g as P, b as e, t as a, i as s, p as C, s as O, m as B, n as Q, u as X, o as g } from './vendor-BOwXO-1K.js'
import { a as Y } from './MainHeader.vue_vue_type_script_setup_true_lang-BsVjIPUp.js'
import { u as Z } from './useDashboardData-CJiI5ZSQ.js'
import { u as ee } from './useProfileData-DtMTXUVJ.js'
import { u as te, a as se, I as ae, b as ie, c as re } from './main-C9ibHt9P.js'
import { I as oe } from './IconArrowLeft-BQwY63on.js'
import './axios-B9ygI19o.js'
const le = { class: 'flex min-h-screen flex-col bg-bgPrimary text-textWhite' },
  ne = { class: 'mx-auto flex w-full max-w-6xl flex-1 flex-col gap-8 px-6 py-10' },
  ce = { class: 'flex flex-col gap-4 rounded-2xl border border-strokePrimary/30 bg-card p-6 lg:flex-row lg:items-center lg:justify-between' },
  de = { class: 'space-y-2' },
  ue = { class: 'text-xs uppercase tracking-[0.3em] text-textSecondary' },
  xe = { class: 'text-3xl font-bold text-textWhite' },
  pe = { class: 'text-sm text-textSecondary' },
  fe = ['aria-label'],
  ve = { class: 'grid gap-6 lg:grid-cols-[1fr_1.2fr]' },
  ge = { class: 'space-y-5 rounded-2xl border border-strokePrimary/30 bg-dialogBg p-6 shadow-2xl shadow-black/20' },
  me = { class: 'flex items-center justify-between' },
  be = { class: 'text-xl font-semibold text-textWhite' },
  he = { class: 'text-sm text-textSecondary' },
  _e = { class: 'rounded-full border border-textSecondary/30 px-3 py-1 text-xs uppercase tracking-[0.3em] text-textSecondary' },
  ye = { class: 'flex flex-col gap-4 rounded-2xl border border-strokePrimary/40 bg-inputBg p-6' },
  Pe = { class: 'flex items-center gap-4' },
  ke = { class: 'relative flex items-center justify-center' },
  we = ['aria-label'],
  Se = ['src', 'alt'],
  De = { key: 1, class: 'text-3xl font-bold uppercase text-textPrimary' },
  Ue = { class: 'space-y-2' },
  Ie = { class: 'text-sm text-textSecondary' },
  Ce = { class: 'text-base text-textWhite' },
  Be = ['aria-label'],
  Le = { class: 'space-y-3' },
  We = { class: 'text-sm font-semibold text-textWhite', for: 'logo-url-input' },
  je = { class: 'flex gap-2' },
  Ne = ['aria-label', 'value'],
  Ae = { class: 'text-xs text-textSecondary' },
  Te = { key: 0, class: 'text-xs text-red-400' },
  Ve = ['aria-label'],
  Fe = { class: 'space-y-3' },
  He = { class: 'text-sm font-semibold text-textWhite', for: 'nickname-input' },
  ze = ['aria-label', 'placeholder', 'value'],
  Me = { class: 'text-xs text-textSecondary' },
  Re = { class: 'space-y-3' },
  Ee = { class: 'text-sm font-semibold text-textWhite', for: 'description-input' },
  qe = ['aria-label', 'placeholder', 'value'],
  $e = { class: 'flex items-center justify-between text-xs' },
  Ge = { class: 'text-textSecondary' },
  Je = { key: 0, class: 'text-xs text-red-400' },
  Ke = { class: 'space-y-3' },
  Oe = ['aria-label', 'disabled'],
  _ = 280,
  it = G({
    __name: 'ProfileSettingsPage',
    setup(Qe) {
      const L = X(),
        { dashboardData: W, loadDashboardData: j } = Z(),
        { profileData: N, loadProfileData: A, refreshProfileData: T } = ee(),
        { t } = te(),
        { notify: k } = re(),
        { refreshUser: V } = se(),
        F = u(() => W.value?.navLinks ?? []),
        f = u(() => N.value?.summary),
        r = n(''),
        c = n(''),
        x = n(''),
        m = n(!1),
        w = n(!1),
        S = u(() => r.value.trim()),
        y = u(() => c.value.length),
        b = n(''),
        D = u(() => !0),
        U = u(() => y.value <= _),
        I = u(() => !!(m.value || !D.value || !U.value))
      ;(J(() => {
        ;(j(), A())
      }),
        K(
          f,
          (i) => {
            if (!i || w.value) return
            const o = i.nickname || '',
              l = i.description || '',
              d = i.avatarUrl || ''
            ;((r.value = d), (c.value = l), (x.value = o), (b.value = d), (w.value = !0))
          },
          { immediate: !0 },
        ))
      const H = () => {
          L.push({ name: 'profile' })
        },
        z = (i) => {
          r.value = i
        },
        M = () => {
          r.value && (r.value = '')
        },
        R = (i) => {
          c.value = i
        },
        h = n(null),
        p = n(null),
        E = () => {
          h.value?.click()
        },
        q = (i) => {
          const o = i.target
          if (o.files && o.files[0]) {
            p.value = o.files[0]
            const l = new FileReader()
            ;((l.onload = (d) => {
              r.value = d.target?.result
            }),
              l.readAsDataURL(o.files[0]))
          }
        },
        $ = async () => {
          if (!I.value) {
            m.value = !0
            try {
              const i = new FormData()
              if (
                (i.append('_method', 'PUT'),
                x.value.trim() && i.append('nickname', x.value.trim()),
                i.append('bio', c.value.trim()),
                p.value ? i.append('avatar', p.value) : r.value && r.value !== b.value && i.append('avatar_url', r.value.trim()),
                p.value ? i.append('avatar', p.value) : r.value && r.value !== b.value && i.append('avatar_url', r.value.trim()),
                await ie.post('/api/users/me', i, { headers: { 'Content-Type': 'multipart/form-data' } }),
                k('success', t('settings.saveSuccess')),
                await T(),
                await V(),
                f.value)
              ) {
                const o = f.value.nickname || '',
                  l = f.value.description || '',
                  d = f.value.avatarUrl || ''
                ;((b.value = d), (r.value = d), (c.value = l), (x.value = o))
              }
              ;((p.value = null), h.value && (h.value.value = ''))
            } catch {
              k('error', t('common.error'))
            } finally {
              m.value = !1
            }
          }
        }
      return (i, o) => (
        g(),
        v('div', le, [
          P(Y, { 'nav-links': F.value }, null, 8, ['nav-links']),
          e('main', ne, [
            e('section', ce, [
              e('div', de, [e('p', ue, a(s(t)('settings.settingsPanel')), 1), e('h1', xe, a(s(t)('settings.title')), 1), e('p', pe, a(s(t)('settings.description')), 1)]),
              e(
                'button',
                { class: 'inline-flex items-center gap-2 rounded-full border border-strokePrimary/40 px-5 py-2 text-sm font-semibold text-textSecondary transition hover:border-textPrimary hover:text-textPrimary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary', 'aria-label': s(t)('settings.backToProfile'), tabindex: '0', onClick: H },
                [P(oe, { class: 'h-4 w-4' }), C(' ' + a(s(t)('settings.backToProfile')), 1)],
                8,
                fe,
              ),
            ]),
            e('section', ve, [
              e('article', ge, [
                e('div', me, [e('div', null, [e('h2', be, a(s(t)('settings.profilePreview')), 1), e('p', he, a(s(t)('settings.previewDescription')), 1)]), e('span', _e, a(s(t)('settings.live')), 1)]),
                e('div', ye, [
                  e('div', Pe, [
                    e('div', ke, [
                      e(
                        'div',
                        { class: 'flex aspect-square w-40 items-center justify-center overflow-hidden rounded-full border border-textPrimary/30 bg-gradient-to-b from-textPrimary/20 to-transparent', 'aria-label': s(t)('settings.logoPreview') },
                        [S.value ? (g(), v('img', { key: 0, src: S.value, alt: s(t)('settings.userLogo'), class: 'h-full w-full select-none object-cover', draggable: 'false' }, null, 8, Se)) : (g(), v('span', De, ' ? '))],
                        8,
                        we,
                      ),
                    ]),
                    e('div', Ue, [e('p', Ie, a(s(t)('settings.yourDescription')), 1), e('p', Ce, a(c.value || s(t)('settings.descriptionPlaceholder')), 1)]),
                  ]),
                ]),
              ]),
              e(
                'form',
                { class: 'space-y-6 rounded-2xl border border-strokePrimary/30 bg-dialogBg p-6 shadow-2xl shadow-black/20', 'aria-label': s(t)('settings.formLabel'), onSubmit: O($, ['prevent']) },
                [
                  e('div', Le, [
                    e('label', We, a(s(t)('settings.logoUrl')), 1),
                    e('input', { id: 'logo-url-input', ref_key: 'fileInput', ref: h, class: 'hidden', type: 'file', accept: 'image/*', onChange: q }, null, 544),
                    e('div', je, [
                      e(
                        'input',
                        {
                          class: 'w-full rounded-2xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary',
                          type: 'text',
                          'aria-label': s(t)('settings.logoUrl'),
                          placeholder: 'https://imgur.com/...',
                          tabindex: '0',
                          value: r.value,
                          onInput: o[0] || (o[0] = (l) => z(l.target.value)),
                        },
                        null,
                        40,
                        Ne,
                      ),
                      e('button', { type: 'button', class: 'shrink-0 rounded-2xl bg-cardHover px-4 font-bold text-textPrimary hover:bg-cardHover/80', onClick: E }, 'Upload'),
                    ]),
                    e('p', Ae, a(s(t)('settings.logoHint')), 1),
                    r.value && !D.value ? (g(), v('p', Te, a(s(t)('settings.invalidUrl')), 1)) : B('', !0),
                    e(
                      'button',
                      { type: 'button', class: 'inline-flex items-center gap-2 rounded-full border border-strokePrimary/40 px-4 py-2 text-xs font-semibold text-textSecondary transition hover:border-textPrimary hover:text-textPrimary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary', 'aria-label': s(t)('settings.clearUrl'), tabindex: '0', onClick: M },
                      a(s(t)('settings.clearUrl')),
                      9,
                      Ve,
                    ),
                  ]),
                  e('div', Fe, [
                    e('label', He, a(s(t)('settings.nickname')), 1),
                    e(
                      'input',
                      {
                        id: 'nickname-input',
                        class: 'w-full rounded-2xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary',
                        type: 'text',
                        'aria-label': s(t)('settings.nickname'),
                        placeholder: s(t)('settings.nicknamePlaceholder'),
                        tabindex: '0',
                        value: x.value,
                        onInput: o[1] || (o[1] = (l) => (x.value = l.target.value)),
                      },
                      null,
                      40,
                      ze,
                    ),
                    e('p', Me, a(s(t)('settings.nicknameHint')), 1),
                  ]),
                  e('div', Re, [
                    e('label', Ee, a(s(t)('settings.profileDescription')), 1),
                    e(
                      'textarea',
                      {
                        id: 'description-input',
                        class: 'h-40 w-full resize-none rounded-2xl border border-strokePrimary/40 bg-inputBg px-4 py-3 text-sm text-textWhite placeholder:text-textSecondary focus:border-textPrimary focus:outline-none focus:ring-1 focus:ring-textPrimary',
                        'aria-label': s(t)('settings.profileDescription'),
                        maxlength: _,
                        placeholder: s(t)('settings.descriptionInputPlaceholder'),
                        tabindex: '0',
                        value: c.value,
                        onInput: o[2] || (o[2] = (l) => R(l.target.value)),
                      },
                      null,
                      40,
                      qe,
                    ),
                    e('div', $e, [e('span', { class: Q(y.value > _ ? 'text-red-400' : 'text-textSecondary') }, a(y.value) + '/' + a(_) + ' ' + a(s(t)('settings.characters')), 3), e('span', Ge, a(s(t)('settings.friendlyMessage')), 1)]),
                    U.value ? B('', !0) : (g(), v('p', Je, a(s(t)('settings.descriptionError')), 1)),
                  ]),
                  e('div', Ke, [
                    e(
                      'button',
                      {
                        type: 'submit',
                        class: 'flex w-full items-center justify-center gap-2 rounded-full bg-textPrimary px-6 py-3 text-sm font-semibold text-bgPrimary transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-textPrimary disabled:cursor-not-allowed disabled:bg-textPrimary/40 disabled:text-bgPrimary/70',
                        'aria-label': s(t)('settings.saveSettings'),
                        tabindex: '0',
                        disabled: I.value,
                      },
                      [P(ae, { class: 'h-4 w-4' }), C(' ' + a(m.value ? s(t)('settings.saving') : s(t)('settings.saveSettings')), 1)],
                      8,
                      Oe,
                    ),
                  ]),
                ],
                40,
                Be,
              ),
            ]),
          ]),
        ])
      )
    },
  })
export { it as default }
