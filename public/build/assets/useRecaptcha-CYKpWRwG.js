import { r as s } from './vendor-BOwXO-1K.js'
const n = window.AppConfig?.recaptchaSiteKey || '6LfwBnAsAAAAACa5hvctvGzdZ1ftlLEP5evRTqO6',
  c = s(!1),
  t = s(!1)
function u() {
  const o = () =>
    c.value
      ? Promise.resolve()
      : document.querySelector('script[src^="https://www.google.com/recaptcha/api.js"]')
        ? ((t.value = !0),
          new Promise((e) => {
            const a = setInterval(() => {
              window.grecaptcha &&
                window.grecaptcha.ready &&
                (clearInterval(a),
                window.grecaptcha.ready(() => {
                  ;((c.value = !0), (t.value = !1), e())
                }))
            }, 100)
          }))
        : t.value
          ? new Promise((e) => {
              const a = setInterval(() => {
                c.value && (clearInterval(a), e())
              }, 100)
            })
          : ((t.value = !0),
            new Promise((e, a) => {
              const r = document.createElement('script')
              ;((r.src = `https://www.google.com/recaptcha/api.js?render=${n}`),
                (r.async = !0),
                (r.defer = !0),
                (r.onload = () => {
                  window.grecaptcha.ready(() => {
                    ;((c.value = !0), (t.value = !1), e())
                  })
                }),
                (r.onerror = () => {
                  ;((t.value = !1), a(new Error('Failed to load reCAPTCHA script')))
                }),
                document.head.appendChild(r))
            }))
  return { getToken: async (e) => (await o(), window.grecaptcha.execute(n, { action: e })), loadScript: o, isLoaded: c }
}
export { u }
