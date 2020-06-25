<script>
    import { Line } from 'vue-chartjs'

    export default {
        extends: Line,
        props: ['data', 'options', 'sequence'],
        mounted () {
            this.renderChart(this.data[this.sequence], this.options)
        }
    }
</script>

<style scoped>

</style>
