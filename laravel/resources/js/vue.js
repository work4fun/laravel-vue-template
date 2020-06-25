import Vue from 'vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import LineChart from './charts/Line';
import Popconfirm from './components/Popconfirm';
import dateFormat from 'dateformat';
import { Loading } from 'element-ui';

Vue.use(ElementUI);

const LOADING_OPTIONS = {
    fullscreen: true
};

const SECOND = 1000;

const ALLOPTION = 'all';
const MAKE_PROJECT_NAMES_OPTIONS_FN = function (projectNames) {
    return Array.from(new Set([ALLOPTION, ...projectNames]));
};

const DATE_PICKER_SHORTCUTS = {
    300000: {
        text: '5 Minutes',
        onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 60 * 5 * SECOND);
            picker.$emit('pick', [start, end]);
        }
    },
    600000: {
        text: '10 Minutes',
        onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 60 * 10 * SECOND);
            picker.$emit('pick', [start, end]);
        }
    },
    900000: {
        text: '15 Minutes',
        onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 60 * 15 * SECOND);
            picker.$emit('pick', [start, end]);
        }
    },
    1800000: {
        text: '30 Minutes',
        onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * SECOND / 2);
            picker.$emit('pick', [start, end]);
        }
    },
    3600000: {
        text: '1 Hour',
        onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 60 * 60 * SECOND);
            picker.$emit('pick', [start, end]);
        }
    },
    10800000: {
        text: '3 Hours',
        onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 60 * 60 * 3 * SECOND);
            picker.$emit('pick', [start, end]);
        }
    },
    21600000: {
        text: '6 Hours',
        onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 60 * 60 * 6 * SECOND);
            picker.$emit('pick', [start, end]);
        }
    },
};

var app = new Vue({
    el: "#app",
    data() {
        const pickerShortcuts = Object.values(DATE_PICKER_SHORTCUTS);

        return {
            query: {
                field: '',
                times: 0,
                rangeDate: [new Date(new Date() - 60 * 15 * SECOND), new Date()],
                projectName: ''
            },

            fields: [],

            datePickerFocused: false,
            dateShortcut: null,

            pickerOptions: {
                shortcuts: pickerShortcuts
            },

            projectNames: [],

            projects: [],

            // line charts data
            // sequence => { field: [ ...data ] }
            projectLineCharts: {

            },

            projectsValues: [],

            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            display: false, // this will remove only the label
                        }
                    }]
                },

                title: {
                    display: true,
                    fontSize: 16,
                    text: 'Custom Chart Title'
                }
            }
        }
    },
    components: {
        LineChart,
        Popconfirm
    },
    delimiters: ['${', '}'],
    mounted() {
        const defaultValuesEl = document.getElementById('default-values');

        let projects = JSON.parse(defaultValuesEl.innerText);

        this.projectsValues = projects;

        this.projectNames = MAKE_PROJECT_NAMES_OPTIONS_FN(projects.map(project => project.project_name));

        for (let projectName of this.projectNames) {
            this.projectLineCharts[projectName] = {};
        }

        let fields = new Set();

        for (let project of projects) {
            fields = new Set([...fields, ...(new Set(project.statistics.map(item => item.field)))])
        }

        this.fields = fields;
    },
    watch: {
        dateShortcut(shortcut) {
            if (shortcut === null
                && window.shortcutTimer
            ) {
                window.clearInterval(window.shortcutTimer);
                return void(0);
            }

            if (window.shortcutTimer) {
                window.clearInterval(window.shortcutTimer);
            }

            window.shortcutTimer = setInterval(() => {
                if (this.datePickerFocused) {
                    return void(0);
                }
                this.query.rangeDate = [new Date(new Date() - shortcut), new Date()];
            }, SECOND)
        },
        projectsValues(projects) {
            let newProjects = [];
            for (let project of projects) {
                let options = JSON.parse(JSON.stringify(this.options));

                options.title.text = project.project_name;
                project.options = options;
                project.renderLineChart = true;

                let sequences = Array.from(new Set(project.statistics.map(item => item.sequence)));

                project.sequenceSelected = sequences[0] || '';

                project.sequences = sequences;

                let formatDateGroup = timestamp => {
                    let date = new Date(timestamp);
                    return dateFormat(date, 'yyyy-mm-dd hh:MM');
                };

                let statisticsSequenceGroupbyed = _.groupBy(project.statistics, item => {
                    return item.sequence;
                });

                let projectSequenceDataCollection = {};

                for (let sequence in statisticsSequenceGroupbyed) {
                    if (!statisticsSequenceGroupbyed.hasOwnProperty(sequence)) {
                        continue;
                    }

                    const sum = (statisticsSequenceGroupbyed[sequence] || { length: 0 }).length;

                    let labels = _.uniq(statisticsSequenceGroupbyed[sequence].map(item => {
                        return formatDateGroup(item.created_at);
                    }).sort());

                    let datasets = [];

                    let statisticsFieldGrouped = _.groupBy(statisticsSequenceGroupbyed[sequence], item => {
                        return item.field;
                    });

                    // 组合line chart 数据
                    // dataset
                    for (let field in statisticsFieldGrouped) {
                        if (!statisticsFieldGrouped.hasOwnProperty(field)) {
                            continue;
                        }

                        let color = this.getRandomColor();
                        let data = [];
                        const fieldSum = statisticsFieldGrouped[field].length; // (7 / 100);
                        let values = _.groupBy(statisticsFieldGrouped[field], item => {
                            return formatDateGroup(item.created_at);
                        });
                        // sort
                        let ordered = {};
                        _(values).keys().sort().each(function (key) {
                            ordered[key] = values[key];
                        });
                        values = ordered;

                        for (let x in values) {
                            if (values.hasOwnProperty(x)) {
                                let y = values[x].length;
                                data.push({
                                    x,
                                    y
                                })
                            }
                        }

                        datasets.push({
                            label: field + ': ' + Math.round((fieldSum / sum * 100)) + '%',
                            backgroundColor: color,
                            borderColor: color,
                            fill: false,
                            data: data
                        });
                    }

                    projectSequenceDataCollection[sequence] = {
                        labels,
                        datasets,
                        sum
                    }
                };

                this.$set(this.projectLineCharts, project.project_name, projectSequenceDataCollection);

                newProjects.push(project);
            }

            this.projects = newProjects;
        }
    },
    methods: {
        onSubmit() {
            let loadingInstance = Loading.service(LOADING_OPTIONS);

            let params = {
                start_time: (this.query.rangeDate[0].getTime() / SECOND),
                end_time: (this.query.rangeDate[1].getTime() / SECOND),
                field: this.query.field,
                times: this.query.times,
            };

            if (this.query.projectName !== ALLOPTION) {
                params['project_name'] = this.query.projectName
            }

            window.axios.get("/api/project.search", {
                params,
            }).then(response => {
                this.projects = [];
                this.$nextTick(() => {
                    this.projectsValues = response.data
                });
            }).catch(error => {
                alert(error);
            }).then(response => {
                window.setTimeout(() => {
                    loadingInstance.close();
                }, SECOND / 2)
            });
        },

        truncateall() {
            window.axios.post('/truncateall', {
            }).then(response => {
                this.projects = [];
            }).catch(error => {
                alert(error);
            }).then(response => {
            });
        },

        onSelectSequence(sequenceSelected, projectIndex) {
            let project = this.projects[projectIndex];
            project.sequenceSelected = sequenceSelected;
            project.renderLineChart = false;
            this.$set(this.projects, projectIndex, project);

            this.$nextTick(() => {
                project.renderLineChart = true;
                this.$set(this.projects, projectIndex, project);
            });
        },

        deleteProject(event) {
            const projectName = event.currentTarget.id;
            window.axios.post('/api/project.delete', {
                project_name: projectName
            }).then(response => {
                let projects = this.projects;
                this.projects = [];
                this.$nextTick(() => {
                    this.projects = projects.filter(project => {
                        return project.project_name !== projectName;
                    });
                });
            }).catch(error => {
                alert(error);
            }).then(response => {
            });
        },

        datePickerFocus() {
            this.datePickerFocused = true;
        },
        datePickerBlur() {
            this.datePickerFocused = false;
        },
        datePickerValueOnChange([date1, date2]) {
            const dateDiff = date2 - date1;
            this.dateShortcut = DATE_PICKER_SHORTCUTS[dateDiff] ? dateDiff : null;

            let params = {
                start_time: (this.query.rangeDate[0].getTime() / SECOND),
                end_time: (this.query.rangeDate[1].getTime() / SECOND),
            };

            // change project names
            window.axios.get('/api/project.names', {
                params
            }).then(response => {
                this.projectNames = MAKE_PROJECT_NAMES_OPTIONS_FN(response.data);
            }).catch(error => {
                window.alert(error);
            })
        },

        injectLineDataStructure() {

        },

        getRandomColor() {
            let letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        },
    }
});
