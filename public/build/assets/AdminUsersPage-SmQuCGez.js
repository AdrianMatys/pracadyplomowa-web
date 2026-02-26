import { a as d, o as r, b as n, d as Y, r as p, e as T, G as Z, m as x, g as L, v as ee, t as i, i as s, x as te, F as W, h as j, p as ne, n as w, s as oe } from './vendor-BOwXO-1K.js'
import { _ as se, a as ae, u as ie, b as _, c as le } from './main-C9ibHt9P.js'
import { _ as N } from './ConfirmationModal.vue_vue_type_script_setup_true_lang-Bnr5TINJ.js'
import './axios-B9ygI19o.js'
const de = {},
  re = { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor' }
function ce(V, l) {
  return (r(), d('svg', re, [...(l[0] || (l[0] = [n('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z' }, null, -1)]))]))
}
const ue = se(de, [['render', ce]]),
  pe = { class: 'space-y-6' },
  me = { class: 'flex items-center justify-between' },
  ve = { class: 'text-3xl font-bold text-textWhite' },
  be = ['placeholder'],
  xe = { class: 'rounded-xl border border-strokePrimary/30 bg-card overflow-hidden' },
  he = { class: 'overflow-x-auto' },
  _e = { class: 'w-full text-left border-collapse' },
  fe = { class: 'bg-bgSecondary text-textSecondary uppercase text-xs' },
  ge = { class: 'px-4 py-4 font-semibold hidden sm:table-cell' },
  ye = { class: 'px-4 py-4 font-semibold' },
  ke = { class: 'px-4 py-4 font-semibold hidden md:table-cell' },
  we = { class: 'px-4 py-4 font-semibold hidden xl:table-cell' },
  Ce = { class: 'px-4 py-4 font-semibold text-right' },
  Se = { class: 'divide-y divide-strokePrimary/20 text-sm' },
  Me = { key: 0 },
  Pe = { colspan: '8', class: 'px-4 py-8 text-center text-textSecondary' },
  $e = { key: 1 },
  De = { colspan: '8', class: 'px-4 py-8 text-center text-textSecondary' },
  Le = { class: 'px-4 py-4 text-textSecondary font-mono hidden sm:table-cell' },
  Ue = { class: 'px-4 py-4 font-medium text-textWhite' },
  Be = { class: 'flex flex-col' },
  ze = { key: 0, class: 'ml-1 px-1.5 py-0.5 bg-primary/20 text-primary text-[10px] rounded uppercase font-bold' },
  Ae = { class: 'text-xs text-textSecondary lg:hidden' },
  Ee = { class: 'px-4 py-4 text-textSecondary hidden lg:table-cell truncate max-w-[200px]' },
  Xe = { class: 'px-4 py-4 hidden md:table-cell' },
  Te = ['value', 'onChange'],
  We = { class: 'px-4 py-4 hidden md:table-cell' },
  je = { class: 'flex items-center gap-1' },
  Ne = ['value', 'onInput'],
  Ve = ['onClick'],
  Ie = { class: 'px-4 py-4 text-textSecondary hidden xl:table-cell whitespace-nowrap' },
  He = { class: 'px-4 py-4' },
  Oe = { class: 'px-4 py-4 text-right' },
  Fe = { class: 'hidden lg:flex items-center justify-end gap-2' },
  Ge = ['disabled', 'onClick'],
  Je = ['onClick'],
  qe = ['disabled', 'onClick'],
  Ke = { class: 'lg:hidden relative actions-dropdown' },
  Qe = ['onClick'],
  Re = { key: 0, class: 'absolute right-0 bottom-full mb-1 w-40 bg-bgPrimary border border-strokePrimary/30 rounded-lg shadow-xl z-20 py-1' },
  Ye = ['disabled', 'onClick'],
  Ze = ['onClick'],
  et = ['disabled', 'onClick'],
  tt = { key: 0, class: 'flex justify-center gap-2 mt-4' },
  nt = ['onClick'],
  lt = Y({
    __name: 'AdminUsersPage',
    setup(V) {
      const { notify: l } = le(),
        { user: v, refreshUser: U } = ae(),
        { t: o } = ie(),
        f = p(null),
        I = (t) => {
          f.value = f.value === t ? null : t
        },
        g = () => {
          f.value = null
        },
        B = (t) => {
          t.target.closest('.actions-dropdown') || g()
        }
      ;(T(() => {
        document.addEventListener('click', B)
      }),
        Z(() => {
          document.removeEventListener('click', B)
        }))
      const y = p([]),
        C = p(!0),
        S = p(''),
        M = p(1),
        P = p(1),
        m = p({}),
        b = p({}),
        $ = p(!1),
        D = p(!1),
        h = p(null),
        c = p(null),
        k = async (t = 1) => {
          C.value = !0
          try {
            const a = await _.get(`/api/admin/users?page=${t}&search=${S.value}`)
            a.data && a.data.data ? ((y.value = a.data.data), (M.value = a.data.current_page), (P.value = a.data.last_page), (m.value = {})) : (y.value = [])
          } catch {
            l('error', o('admin.users.notifications.fetchError'))
          } finally {
            C.value = !1
          }
        },
        H = (t, a) => {
          m.value[t] = a
        },
        z = async (t) => {
          const a = m.value[t.id]
          if (a)
            try {
              const e = await _.patch(`/api/admin/users/${t.id}/level`, { level: a })
              ;((t.level = e.data.user.level), delete m.value[t.id], l('success', o('admin.users.notifications.levelUpdated')), t.id === v.value?.id && (await U()))
            } catch {
              l('error', o('admin.users.notifications.levelError'))
            }
        },
        A = (t) => m.value[t.id] !== void 0 && m.value[t.id] !== t.level,
        O = (t, a) => {
          b.value[t] = a
        },
        F = async (t) => {
          const a = b.value[t.id]
          if (a !== void 0)
            try {
              const e = await _.patch(`/api/admin/users/${t.id}/xp`, { experience_points: a })
              ;(t.profile && (t.profile.experience_points = a), e.data.user && e.data.user.level && ((t.level = e.data.user.level), m.value[t.id] === t.level && delete m.value[t.id]), delete b.value[t.id], l('success', 'XP zaktualizowane'), t.id === v.value?.id && (await U()))
            } catch {
              l('error', 'Błąd aktualizacji XP')
            }
        },
        G = (t) => b.value[t.id] !== void 0 && b.value[t.id] !== (t.profile?.experience_points ?? 0),
        E = (t) => {
          if (t.id === v.value?.id) {
            l('error', o('admin.users.notifications.banSelf'))
            return
          }
          ;((c.value = t), (D.value = !0))
        },
        J = async () => {
          if (c.value)
            try {
              const t = await _.patch(`/api/admin/users/${c.value.id}/ban`)
              ;((c.value.is_banned = t.data.user.is_banned), l('success', c.value.is_banned ? o('admin.users.notifications.bannedSuccess') : o('admin.users.notifications.unbannedSuccess')))
            } catch {
              l('error', o('admin.users.notifications.statusError'))
            }
        },
        X = (t) => {
          if (t.id === v.value?.id) {
            l('error', o('admin.users.notifications.deleteSelf'))
            return
          }
          ;((h.value = t), ($.value = !0))
        },
        q = async () => {
          if (h.value)
            try {
              ;(await _.delete(`/api/admin/users/${h.value.id}`), l('success', o('admin.users.notifications.userDeleted')), k(M.value))
            } catch {
              l('error', o('admin.users.notifications.deleteError'))
            }
        },
        K = (t) => {
          ;(E(t), g())
        },
        Q = (t) => {
          ;(z(t), g())
        },
        R = (t) => {
          ;(X(t), g())
        }
      return (
        T(() => k()),
        (t, a) => (
          r(),
          d('div', pe, [
            n('div', me, [
              n('h1', ve, i(s(o)('admin.users.title')), 1),
              ee(n('input', { 'onUpdate:modelValue': a[0] || (a[0] = (e) => (S.value = e)), type: 'text', placeholder: s(o)('admin.users.searchPlaceholder'), class: 'bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none transition-colors w-64', onInput: a[1] || (a[1] = (e) => k(1)) }, null, 40, be), [[te, S.value]]),
            ]),
            n('div', xe, [
              n('div', he, [
                n('table', _e, [
                  n('thead', fe, [
                    n('tr', null, [
                      n('th', ge, i(s(o)('admin.common.id')), 1),
                      n('th', ye, i(s(o)('admin.users.table.nickname')), 1),
                      a[4] || (a[4] = n('th', { class: 'px-4 py-4 font-semibold hidden lg:table-cell' }, 'Email', -1)),
                      n('th', ke, i(s(o)('admin.users.table.level') || 'Level'), 1),
                      a[5] || (a[5] = n('th', { class: 'px-4 py-4 font-semibold hidden md:table-cell' }, 'XP', -1)),
                      n('th', we, i(s(o)('admin.common.createdAt')), 1),
                      a[6] || (a[6] = n('th', { class: 'px-4 py-4 font-semibold' }, 'Status', -1)),
                      n('th', Ce, i(s(o)('admin.common.actions')), 1),
                    ]),
                  ]),
                  n('tbody', Se, [
                    C.value
                      ? (r(), d('tr', Me, [n('td', Pe, i(s(o)('admin.common.loading')), 1)]))
                      : y.value.length === 0
                        ? (r(), d('tr', $e, [n('td', De, i(s(o)('admin.users.noUsers')), 1)]))
                        : (r(!0),
                          d(
                            W,
                            { key: 2 },
                            j(
                              y.value,
                              (e) => (
                                r(),
                                d('tr', { key: e.id, class: 'hover:bg-cardHover transition-colors' }, [
                                  n('td', Le, i(e.id), 1),
                                  n('td', Ue, [n('div', Be, [n('span', null, [ne(i(e.profile?.nickname || s(o)('admin.users.noNickname')) + ' ', 1), e.isAdmin ? (r(), d('span', ze, 'Admin')) : x('', !0)]), n('span', Ae, i(e.email), 1)])]),
                                  n('td', Ee, i(e.email), 1),
                                  n('td', Xe, [
                                    n(
                                      'select',
                                      { value: m.value[e.id] ?? e.level, class: 'bg-bgSecondary border border-strokePrimary/30 rounded px-2 py-1 text-textWhite text-xs focus:border-primary focus:outline-none w-full max-w-[100px]', onChange: (u) => H(e.id, u.target.value) },
                                      [...(a[7] || (a[7] = [n('option', { value: 'junior' }, 'Junior', -1), n('option', { value: 'mid' }, 'Mid', -1), n('option', { value: 'senior' }, 'Senior', -1)]))],
                                      40,
                                      Te,
                                    ),
                                  ]),
                                  n('td', We, [
                                    n('div', je, [
                                      n('input', { type: 'number', value: b.value[e.id] ?? e.profile?.experience_points ?? 0, class: 'bg-bgSecondary border border-strokePrimary/30 rounded px-2 py-1 text-textWhite text-xs focus:border-primary focus:outline-none w-16', onInput: (u) => O(e.id, parseInt(u.target.value) || 0) }, null, 40, Ne),
                                      G(e) ? (r(), d('button', { key: 0, class: 'px-2 py-1 rounded bg-green-500/10 text-green-500 hover:bg-green-500/20 transition-colors text-xs font-bold shrink-0', onClick: (u) => F(e) }, '✓', 8, Ve)) : x('', !0),
                                    ]),
                                  ]),
                                  n('td', Ie, i(new Date(e.created_at).toLocaleDateString()), 1),
                                  n('td', He, [n('span', { class: w(['px-2 py-1 rounded text-xs font-bold uppercase whitespace-nowrap', e.is_banned ? 'bg-red-500/10 text-red-500' : 'bg-green-500/10 text-green-500']) }, i(e.is_banned ? s(o)('admin.users.banned') : s(o)('admin.users.active')), 3)]),
                                  n('td', Oe, [
                                    n('div', Fe, [
                                      n(
                                        'button',
                                        { disabled: e.id === s(v)?.id, class: w(['px-3 py-1.5 rounded transition-colors text-xs font-bold disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap', e.is_banned ? 'bg-green-500/10 text-green-500 hover:bg-green-500/20' : 'bg-amber-500/10 text-amber-500 hover:bg-amber-500/20']), onClick: (u) => E(e) },
                                        i(e.is_banned ? s(o)('admin.users.unban') : s(o)('admin.users.ban')),
                                        11,
                                        Ge,
                                      ),
                                      A(e) ? (r(), d('button', { key: 0, class: 'px-3 py-1.5 rounded bg-blue-500/10 text-blue-500 hover:bg-blue-500/20 transition-colors text-xs font-bold whitespace-nowrap', onClick: (u) => z(e) }, i(s(o)('admin.common.save')), 9, Je)) : x('', !0),
                                      n('button', { disabled: e.id === s(v)?.id, class: 'px-3 py-1.5 rounded bg-red-500/10 text-red-500 hover:bg-red-500/20 transition-colors text-xs font-bold disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap', onClick: (u) => X(e) }, i(s(o)('admin.common.delete')), 9, qe),
                                    ]),
                                    n('div', Ke, [
                                      n('button', { class: 'p-2 rounded bg-bgSecondary text-textSecondary hover:text-textWhite hover:bg-cardHover transition-colors', onClick: oe((u) => I(e.id), ['stop']) }, [L(ue, { class: 'h-5 w-5' })], 8, Qe),
                                      f.value === e.id
                                        ? (r(),
                                          d('div', Re, [
                                            n('button', { disabled: e.id === s(v)?.id, class: w(['w-full text-left px-4 py-2 text-xs font-bold transition-colors disabled:opacity-50', e.is_banned ? 'text-green-500 hover:bg-green-500/10' : 'text-amber-500 hover:bg-amber-500/10']), onClick: (u) => K(e) }, i(e.is_banned ? s(o)('admin.users.unban') : s(o)('admin.users.ban')), 11, Ye),
                                            A(e) ? (r(), d('button', { key: 0, class: 'w-full text-left px-4 py-2 text-xs font-bold text-blue-500 hover:bg-blue-500/10 transition-colors', onClick: (u) => Q(e) }, i(s(o)('admin.common.save')), 9, Ze)) : x('', !0),
                                            n('button', { disabled: e.id === s(v)?.id, class: 'w-full text-left px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-500/10 transition-colors disabled:opacity-50', onClick: (u) => R(e) }, i(s(o)('admin.common.delete')), 9, et),
                                          ]))
                                        : x('', !0),
                                    ]),
                                  ]),
                                ])
                              ),
                            ),
                            128,
                          )),
                  ]),
                ]),
              ]),
            ]),
            P.value > 1
              ? (r(),
                d('div', tt, [
                  (r(!0),
                  d(
                    W,
                    null,
                    j(P.value, (e) => (r(), d('button', { key: e, class: w(['w-8 h-8 rounded-lg text-xs font-bold transition-colors', M.value === e ? 'bg-primary text-textWhite' : 'bg-bgSecondary text-textSecondary hover:bg-cardHover']), onClick: (u) => k(e) }, i(e), 11, nt))),
                    128,
                  )),
                ]))
              : x('', !0),
            L(N, { 'is-open': $.value, 'onUpdate:isOpen': a[2] || (a[2] = (e) => ($.value = e)), title: s(o)('admin.users.deleteModalTitle'), message: s(o)('admin.users.deleteModalMessage', { name: h.value?.profile?.nickname || h.value?.email || '' }), 'confirm-text': s(o)('admin.common.delete'), 'cancel-text': s(o)('admin.common.cancel'), 'is-danger': '', onConfirm: q }, null, 8, [
              'is-open',
              'title',
              'message',
              'confirm-text',
              'cancel-text',
            ]),
            L(
              N,
              {
                'is-open': D.value,
                'onUpdate:isOpen': a[3] || (a[3] = (e) => (D.value = e)),
                title: c.value?.is_banned ? s(o)('admin.users.unbanModalTitle') : s(o)('admin.users.banModalTitle'),
                message: c.value?.is_banned ? s(o)('admin.users.unbanModalMessage', { name: c.value?.profile?.nickname || c.value?.email || '' }) : s(o)('admin.users.banModalMessage', { name: c.value?.profile?.nickname || c.value?.email || '' }),
                'confirm-text': c.value?.is_banned ? s(o)('admin.users.unban') : s(o)('admin.users.ban'),
                'cancel-text': s(o)('admin.common.cancel'),
                'is-danger': !c.value?.is_banned,
                onConfirm: J,
              },
              null,
              8,
              ['is-open', 'title', 'message', 'confirm-text', 'cancel-text', 'is-danger'],
            ),
          ])
        )
      )
    },
  })
export { lt as default }
