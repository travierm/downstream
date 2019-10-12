
export default class DownstreamAnalytics {
    recordMediaPlay(mediaId) {
        const data = {
            media_id:mediaId
        };

        axios.post('/api/ana/media/play', data);
    }
}