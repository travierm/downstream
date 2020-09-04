const shouldLog = false;

export default function consl(message) {
  if (shouldLog) {
    console.info(message);
  }
}
