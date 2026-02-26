import { d as I, r as c, B as H, c as K, e as O, a as n, b as e, m, t, i as l, v as z, E as R, x as T, p as V, F as W, h as A, s as B, n as j, C as J, o as d } from './vendor-BOwXO-1K.js'
import { u as Q, b as U, _ as q } from './main-C9ibHt9P.js'
import './axios-B9ygI19o.js'
const G = { class: 'space-y-6 h-full flex flex-col relative' },
  X = { class: 'flex items-center justify-between' },
  Y = { class: 'text-3xl font-bold text-textWhite' },
  Z = { key: 0, class: 'text-red-500 text-sm bg-red-500/10 px-4 py-2 rounded' },
  ee = { class: 'flex gap-3 items-center' },
  te = { class: 'flex items-center gap-2 mr-4' },
  se = { class: 'text-xs text-textSecondary uppercase font-bold' },
  oe = ['placeholder'],
  ae = { class: 'flex-1 rounded-xl border border-strokePrimary/30 bg-card overflow-hidden flex flex-col' },
  re = { class: 'px-6 py-3 border-b border-strokePrimary/20 flex justify-between items-center bg-bgSecondary/20' },
  le = { class: 'text-xs text-textSecondary uppercase font-bold tracking-wider' },
  ne = { class: 'text-textWhite' },
  de = { class: 'text-xs text-textSecondary italic' },
  ie = { class: 'overflow-auto flex-1 custom-scrollbar' },
  ce = { class: 'w-full text-left border-collapse' },
  ue = { class: 'bg-bgSecondary text-textSecondary uppercase text-xs sticky top-0 z-10' },
  xe = { class: 'px-6 py-4 font-semibold w-40' },
  pe = { class: 'px-6 py-4 font-semibold w-40' },
  be = { class: 'px-6 py-4 font-semibold w-32' },
  me = { class: 'px-6 py-4 font-semibold' },
  ve = { class: 'px-6 py-4 font-semibold w-24 text-right' },
  ye = { class: 'divide-y divide-strokePrimary/20 text-sm' },
  ge = { key: 0 },
  fe = { colspan: '5', class: 'px-6 py-8 text-center text-textSecondary' },
  he = { key: 1 },
  _e = { colspan: '5', class: 'px-6 py-8 text-center text-textSecondary' },
  ke = { class: 'px-6 py-3 text-textSecondary text-xs font-mono whitespace-nowrap' },
  we = { class: 'px-6 py-3 text-textWhite font-medium text-xs' },
  Se = { class: 'flex flex-col' },
  Pe = { class: 'text-[10px] text-textSecondary' },
  Ce = { class: 'px-6 py-3' },
  ze = { class: 'px-6 py-3 text-textWhite font-medium truncate max-w-xl' },
  We = { class: 'px-6 py-3 text-right' },
  je = ['onClick'],
  Le = { key: 0, class: 'p-4 border-t border-strokePrimary/30 flex justify-center items-center gap-2 bg-bgSecondary/20' },
  $e = ['disabled'],
  Me = ['onClick'],
  De = { key: 1, class: 'px-2 text-textSecondary font-bold' },
  Ne = ['disabled'],
  Te = { class: 'bg-bgPrimary border border-strokePrimary rounded-2xl w-full max-w-2xl flex flex-col shadow-2xl overflow-hidden' },
  Ve = { class: 'p-6 border-b border-strokePrimary/30 flex justify-between items-center bg-bgSecondary/30' },
  Ae = { class: 'flex items-center gap-3' },
  Be = { class: 'text-textSecondary text-sm font-mono' },
  Ue = { class: 'p-6 space-y-4' },
  Ee = { class: 'grid grid-cols-2 gap-4' },
  Fe = { class: 'text-textWhite' },
  Ie = { class: 'text-textWhite' },
  He = { key: 0 },
  Ke = { class: 'bg-[#0d1117] p-3 rounded-lg text-xs font-mono text-gray-300 whitespace-pre-wrap' },
  Oe = { class: 'p-4 border-t border-strokePrimary/30 flex justify-end bg-bgSecondary/30' },
  Re = { class: 'bg-bgPrimary border border-red-500/30 rounded-2xl w-full max-w-md flex flex-col shadow-2xl overflow-hidden' },
  Je = { class: 'p-6 border-b border-strokePrimary/30 bg-red-500/5' },
  Qe = { class: 'text-xl font-bold text-textWhite' },
  qe = { class: 'p-6 space-y-4' },
  Ge = { class: 'text-sm text-textSecondary' },
  Xe = { class: 'space-y-2' },
  Ye = { class: 'text-xs text-textSecondary uppercase font-bold block' },
  Ze = { key: 0, class: 'text-xs text-red-500 bg-red-500/10 p-2 rounded' },
  et = { class: 'p-4 border-t border-strokePrimary/30 flex justify-end gap-3 bg-bgSecondary/30' },
  tt = ['disabled'],
  st = { key: 0, class: 'w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin' },
  ot = I({
    __name: 'AdminLogsPage',
    setup(at) {
      const h = c([]),
        S = c(!0),
        i = c(null),
        _ = c(''),
        u = c(1),
        p = c(1),
        k = c(50),
        L = c(0),
        P = c(null),
        y = c(!1),
        b = c(''),
        g = c(!1),
        v = c(null),
        { t: a } = Q()
      let $
      const f = async (r = 1) => {
        S.value = !0
        try {
          const s = (await U.get('/api/admin/logs', { params: { page: r, search: _.value, per_page: k.value } })).data
          ;((h.value = s.data), (u.value = s.current_page), (p.value = s.last_page), (L.value = s.total))
        } catch (o) {
          ;(console.error('Failed to fetch logs:', o), (P.value = o.message || 'Unknown error'), (h.value = []))
        } finally {
          S.value = !1
        }
      }
      H([_, k], () => {
        ;(clearTimeout($),
          ($ = setTimeout(() => {
            ;((u.value = 1), f(1))
          }, 300)))
      })
      const M = (r) => new Date(r).toLocaleString(),
        D = (r) => (r.includes('error') || r.includes('fail') ? 'text-red-400 bg-red-500/10' : r.includes('warning') ? 'text-amber-400 bg-amber-500/10' : r.includes('login') || r.includes('register') ? 'text-green-400 bg-green-500/10' : r.includes('achievement') ? 'text-purple-400 bg-purple-500/10' : 'text-blue-400 bg-blue-500/10'),
        C = (r) => {
          r >= 1 && r <= p.value && f(r)
        },
        N = async () => {
          if (!b.value) {
            v.value = 'Hasło jest wymagane.'
            return
          }
          ;((g.value = !0), (v.value = null))
          try {
            ;(await U.delete('/api/admin/logs/clear', { data: { password: b.value } }), (y.value = !1), (b.value = ''), f(1))
          } catch (r) {
            ;(console.error('Failed to clear logs:', r), (v.value = r.response?.data?.message || 'Błąd podczas czyszczenia logów.'))
          } finally {
            g.value = !1
          }
        },
        E = K(() => {
          const r = [],
            s = u.value - 2,
            w = u.value + 2 + 1
          for (let x = 1; x <= p.value; x++) x === 1 || x === p.value || (x >= s && x < w) ? r.push(x) : (x === s - 1 || x === w) && r.push('...')
          return r
        }),
        F = () => {
          ;((y.value = !1), (b.value = ''), (v.value = null))
        }
      return (
        O(() => f()),
        (r, o) => (
          d(),
          n('div', G, [
            e('div', X, [
              e('h1', Y, t(l(a)('admin.logs.title')), 1),
              P.value ? (d(), n('div', Z, 'Error: ' + t(P.value), 1)) : m('', !0),
              e('div', ee, [
                e('div', te, [
                  e('span', se, t(l(a)('admin.logs.perPage') || 'Logów na stronę'), 1),
                  z(
                    e(
                      'select',
                      { 'onUpdate:modelValue': o[0] || (o[0] = (s) => (k.value = s)), class: 'bg-bgSecondary border border-strokePrimary/30 rounded-lg px-2 py-2 text-textWhite text-sm focus:border-primary focus:outline-none transition-colors' },
                      [...(o[11] || (o[11] = [e('option', { value: 10 }, '10', -1), e('option', { value: 20 }, '20', -1), e('option', { value: 50 }, '50', -1), e('option', { value: 100 }, '100', -1), e('option', { value: 250 }, '250', -1)]))],
                      512,
                    ),
                    [[R, k.value]],
                  ),
                ]),
                z(e('input', { 'onUpdate:modelValue': o[1] || (o[1] = (s) => (_.value = s)), type: 'text', placeholder: l(a)('admin.logs.searchPlaceholder'), class: 'bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-primary focus:outline-none transition-colors w-64' }, null, 8, oe), [[T, _.value]]),
                e('button', { class: 'px-4 py-2 bg-red-500/10 border border-red-500/30 text-red-500 rounded-lg text-sm font-semibold hover:bg-red-500/20 transition-colors', onClick: o[2] || (o[2] = (s) => (y.value = !0)) }, t(l(a)('admin.logs.clear') || 'Wyczyść logi'), 1),
                e('button', { class: 'px-4 py-2 bg-bgSecondary border border-strokePrimary/30 rounded-lg text-sm font-semibold hover:bg-cardHover transition-colors', onClick: o[3] || (o[3] = (s) => f(u.value)) }, t(l(a)('admin.logs.refresh')), 1),
              ]),
            ]),
            e('div', ae, [
              e('div', re, [e('div', le, [V(t(l(a)('admin.logs.total') || 'Razem logów') + ': ', 1), e('span', ne, t(L.value), 1)]), e('div', de, t(l(a)('admin.logs.showingPage') || 'Strona') + ' ' + t(u.value) + ' / ' + t(p.value), 1)]),
              e('div', ie, [
                e('table', ce, [
                  e('thead', ue, [e('tr', null, [e('th', xe, t(l(a)('admin.common.date')), 1), e('th', pe, t(l(a)('admin.logs.table.user')), 1), e('th', be, t(l(a)('admin.logs.table.action')), 1), e('th', me, t(l(a)('admin.logs.table.description')), 1), e('th', ve, t(l(a)('admin.common.actions')), 1)])]),
                  e('tbody', ye, [
                    S.value
                      ? (d(), n('tr', ge, [e('td', fe, t(l(a)('admin.logs.loading')), 1)]))
                      : h.value.length === 0
                        ? (d(), n('tr', he, [e('td', _e, t(l(a)('admin.logs.empty')), 1)]))
                        : (d(!0),
                          n(
                            W,
                            { key: 2 },
                            A(
                              h.value,
                              (s) => (
                                d(),
                                n('tr', { key: s.id, class: 'hover:bg-cardHover transition-colors group' }, [
                                  e('td', ke, t(M(s.created_at)), 1),
                                  e('td', we, [e('div', Se, [e('span', null, t(s.user_name || 'N/A'), 1), e('span', Pe, 'ID: ' + t(s.user_id), 1)])]),
                                  e('td', Ce, [e('span', { class: j(['px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider', D(s.action)]) }, t(s.action), 3)]),
                                  e('td', ze, t(s.description), 1),
                                  e('td', We, [e('button', { class: 'px-3 py-1.5 rounded bg-bgSecondary text-textSecondary hover:text-textWhite hover:bg-strokePrimary transition-colors text-xs font-bold', onClick: (w) => (i.value = s) }, t(l(a)('admin.common.details')), 9, je)]),
                                ])
                              ),
                            ),
                            128,
                          )),
                  ]),
                ]),
              ]),
              p.value > 1
                ? (d(),
                  n('div', Le, [
                    e('button', { disabled: u.value === 1, class: 'h-8 w-8 flex items-center justify-center rounded bg-bgSecondary text-textSecondary hover:text-textWhite disabled:opacity-50 transition-colors', onClick: o[4] || (o[4] = (s) => C(u.value - 1)) }, '<', 8, $e),
                    (d(!0),
                    n(
                      W,
                      null,
                      A(E.value, (s) => (d(), n(W, { key: s }, [s !== '...' ? (d(), n('button', { key: 0, class: j(['h-8 w-8 flex items-center justify-center rounded text-sm font-semibold transition-all', [u.value === s ? 'bg-textPrimary text-bgPrimary' : 'bg-bgSecondary text-textSecondary hover:text-textWhite']]), onClick: (w) => C(s) }, t(s), 11, Me)) : (d(), n('span', De, t(s), 1))], 64))),
                      128,
                    )),
                    e('button', { disabled: u.value === p.value, class: 'h-8 w-8 flex items-center justify-center rounded bg-bgSecondary text-textSecondary hover:text-textWhite disabled:opacity-50 transition-colors', onClick: o[5] || (o[5] = (s) => C(u.value + 1)) }, '>', 8, Ne),
                  ]))
                : m('', !0),
            ]),
            i.value
              ? (d(),
                n('div', { key: 0, class: 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm', onClick: o[8] || (o[8] = B((s) => (i.value = null), ['self'])) }, [
                  e('div', Te, [
                    e('div', Ve, [e('div', Ae, [e('span', { class: j(['px-2 py-1 rounded text-xs font-bold uppercase', D(i.value.action)]) }, t(i.value.action), 3), e('span', Be, t(M(i.value.created_at)), 1)]), e('button', { class: 'text-textSecondary hover:text-textWhite transition-colors p-2', onClick: o[6] || (o[6] = (s) => (i.value = null)) }, '✕')]),
                    e('div', Ue, [
                      e('div', Ee, [e('div', null, [o[12] || (o[12] = e('label', { class: 'text-xs text-textSecondary uppercase font-bold block mb-1' }, 'User', -1)), e('div', Fe, t(i.value.user_name || 'N/A') + ' (ID: ' + t(i.value.user_id) + ')', 1)])]),
                      e('div', null, [o[13] || (o[13] = e('label', { class: 'text-xs text-textSecondary uppercase font-bold block mb-1' }, 'Description', -1)), e('div', Ie, t(i.value.description), 1)]),
                      i.value.metadata ? (d(), n('div', He, [o[14] || (o[14] = e('label', { class: 'text-xs text-textSecondary uppercase font-bold block mb-1' }, 'Metadata', -1)), e('pre', Ke, t(JSON.stringify(i.value.metadata, null, 2)), 1)])) : m('', !0),
                    ]),
                    e('div', Oe, [e('button', { class: 'px-6 py-2 bg-textPrimary text-bgPrimary font-bold rounded-lg hover:opacity-90 transition-opacity', onClick: o[7] || (o[7] = (s) => (i.value = null)) }, t(l(a)('admin.common.close')), 1)]),
                  ]),
                ]))
              : m('', !0),
            y.value
              ? (d(),
                n('div', { key: 1, class: 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm', onClick: o[10] || (o[10] = B((s) => (y.value = !1), ['self'])) }, [
                  e('div', Re, [
                    e('div', Je, [e('h3', Qe, t(l(a)('admin.logs.clearModal.title') || 'Wyczyścić logi?'), 1)]),
                    e('div', qe, [
                      e('p', Ge, t(l(a)('admin.logs.clearModal.description') || 'Tej operacji nie można cofnąć. Wszystkie dotychczasowe logi zostaną usunięte z bazy danych. W celu potwierdzenia wpisz hasło do swojego konta administratora.'), 1),
                      e('div', Xe, [
                        e('label', Ye, t(l(a)('login.passwordLabel')), 1),
                        z(e('input', { 'onUpdate:modelValue': o[9] || (o[9] = (s) => (b.value = s)), type: 'password', placeholder: '••••••••', class: 'w-full bg-bgSecondary border border-strokePrimary/30 rounded-lg px-4 py-2 text-textWhite text-sm focus:border-red-500 focus:outline-none transition-colors', onKeyup: J(N, ['enter']) }, null, 544), [[T, b.value]]),
                      ]),
                      v.value ? (d(), n('div', Ze, t(v.value), 1)) : m('', !0),
                    ]),
                    e('div', et, [
                      e('button', { class: 'px-4 py-2 rounded-lg text-sm font-semibold text-textSecondary hover:text-textWhite transition-colors', onClick: F }, t(l(a)('common.cancel')), 1),
                      e('button', { class: 'px-6 py-2 bg-red-500 text-white font-bold rounded-lg hover:bg-red-600 transition-colors disabled:opacity-50 flex items-center gap-2', disabled: g.value || !b.value, onClick: N }, [g.value ? (d(), n('span', st)) : m('', !0), V(' ' + t(g.value ? l(a)('common.loading') : l(a)('admin.logs.clearConfirm') || 'Potwierdzam i czyszczę'), 1)], 8, tt),
                    ]),
                  ]),
                ]))
              : m('', !0),
          ])
        )
      )
    },
  }),
  dt = q(ot, [['__scopeId', 'data-v-1b9b2c2f']])
export { dt as default }
