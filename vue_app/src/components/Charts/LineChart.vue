<template>
    <div>
        <apexchart ref="chart" class="mt-2" height="250" type="line" :options="chart.options" :series="chart.series" />
    </div>
</template>

<script>
export default {
    name: "LineChart",
    props: {
        seriesName: {
            type: String,
            required: true,
        },
        seriesColor: {
            type: String,
            required: false,
            default: '#24b6ff',
        },
        title: {
            type: String,
            required: true,
        },
        chartData: {
            type: Object,
            required: true,
        },
        hideBottomLabels: {
            type: Boolean,
            default: false
        }
    },
    components: {},
    data: function() {
        return {
            chart: {
                series: [
                    {
                        name: this.seriesName,
                        data: [],
                        color: this.seriesColor
                    },
                ],
                options: {
                    theme: {
                        mode: 'dark',
                        palette: 'palette4',
                    },
                    chart: {
                        height: 350,
                        type: 'line',
                        zoom: {
                            enabled: false,
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        curve: 'straight',
                    },
                    title: {
                        text: this.title,
                        align: 'left',
                    },
                    grid: {
                        row: {
                            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                            opacity: 0.2,
                        },
                    },
                    xaxis: {
                        categories: [],
                         labels: {
                            show: this.hideBottomLabels === false,
                        }
                    },
                },
            },
        }
    },
    mounted() {
        this.chart.series[0].data = this.chartData.data
        this.chart.options.xaxis = {
            categories: this.chartData.categories,
        }
        this.$refs.chart.updateOptions({
            ...this.chart.options,
            xaxis: {
                categories: this.chartData.categories,
            },
        })
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
