const app = new Vue({
    el: '#noapp',
    data: {
        channelsData: null,
        selectedChannel: null,
        currentPage: 1,
        perPage: 20,
    },
    computed: {
        paginatedVideos() {
            if (!this.selectedChannel) return [];
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.selectedChannel.youtube_channel_videos.slice(start, end);
        },
        totalVideos() {
            return this.selectedChannel ? this.selectedChannel.youtube_channel_videos.length : 0;
        },
        totalPages() {
            return Math.ceil(this.totalVideos / this.perPage);
        },
    },
    methods: {
        selectChannel(channel) {
            this.selectedChannel = channel;
            this.currentPage = 1;
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
    },
    mounted() {
        fetch('youtube_channel_json.php')
            .then(response => response.json())
            .then(data => {
              this.channelsData = data.youtube_channels;
            })
            .catch((error) => {
                console.error('Error loading data:', error);
            });
    },
});



