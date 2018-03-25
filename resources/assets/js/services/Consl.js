const shouldLog = true;

export default function consl(message) {
	if(shouldLog) {
		console.info(message);
	}
}