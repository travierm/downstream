import { build, createLogger } from 'vite'

const logger = createLogger()
const warn = logger.warn
let warnings = 0
logger.warn = (...args) => {
  warnings++
  warn(...args)
}

await build({ customLogger: logger })
if (warnings) {
  console.error(`Build produced ${warnings} warning(s). Resolve them before shipping.`)
  process.exitCode = 1
}
