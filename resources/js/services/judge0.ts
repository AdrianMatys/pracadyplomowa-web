export type Judge0Submission = {
  source_code: string
  language_id: number
  stdin?: string
  expected_output?: string
}

export type Judge0Response = {
  token: string
}

export type Judge0Status = {
  id: number
  description: string
}

export type Judge0Result = {
  stdout: string | null
  stderr: string | null
  compile_output: string | null
  message: string | null
  status: Judge0Status
  time: string
  memory: string
}

const API_URL = 'https://judge0-ce.p.rapidapi.com'
const API_KEY = '5af29ad5b5msh36aa46c130a5c53p1bca7cjsnc5e970eec62e'

const toBase64 = (str: string): string => {
  try {
    return btoa(unescape(encodeURIComponent(str)))
  } catch (e) {
    console.error('Error encoding to Base64:', e)
    return ''
  }
}

const fromBase64 = (str: string | null): string | null => {
  if (!str) return null
  try {
    return decodeURIComponent(escape(atob(str)))
  } catch (e) {
    console.error('Error decoding from Base64:', e)
    return str
  }
}

export const createSubmission = async (submission: Judge0Submission): Promise<string> => {
  const payload = {
    ...submission,
    source_code: toBase64(submission.source_code),
    stdin: submission.stdin ? toBase64(submission.stdin) : undefined,
    expected_output: submission.expected_output ? toBase64(submission.expected_output) : undefined,
  }

  const response = await fetch(`${API_URL}/submissions?base64_encoded=true&fields=*`, {
    method: 'POST',
    headers: {
      'content-type': 'application/json',
      'Content-Type': 'application/json',
      'X-RapidAPI-Key': API_KEY,
      'X-RapidAPI-Host': 'judge0-ce.p.rapidapi.com',
    },
    body: JSON.stringify(payload),
  })

  if (!response.ok) {
    const errorBody = await response.text()
    throw new Error(`Failed to create submission: ${response.status} ${response.statusText} - ${errorBody}`)
  }

  const data = (await response.json()) as Judge0Response
  return data.token
}

export const getSubmission = async (token: string): Promise<Judge0Result> => {
  const response = await fetch(`${API_URL}/submissions/${token}?base64_encoded=true&fields=*`, {
    method: 'GET',
    headers: {
      'X-RapidAPI-Key': API_KEY,
      'X-RapidAPI-Host': 'judge0-ce.p.rapidapi.com',
    },
  })

  if (!response.ok) {
    throw new Error('Failed to get submission')
  }

  const data = (await response.json()) as Judge0Result

  return {
    ...data,
    stdout: fromBase64(data.stdout),
    stderr: fromBase64(data.stderr),
    compile_output: fromBase64(data.compile_output),
    message: fromBase64(data.message),
  }
}
