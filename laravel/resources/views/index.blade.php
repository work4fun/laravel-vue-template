<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div id="app">
            <el-form :inline="true" :model="query" class="demo-form-inline">
                <el-form-item label="条件">
                    <el-select v-model="query.field" filterable placeholder="Select">
                        <el-option
                            v-for="field in fields"
                            :key="field"
                            :label="field"
                            :value="field">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="次数">
                    <el-input v-model="query.times" placeholder=""></el-input>
                </el-form-item>
                <el-form-item label="项目">
                    <el-select v-model="query.projectName" filterable placeholder="Select">
                        <el-option
                            v-for="name in projectNames"
                            :key="name"
                            :label="name"
                            :value="name">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="时间区间">
                    <el-date-picker
                        ref="datepicker"
                        range-separator="To"
                        v-model="query.rangeDate"
                        type="datetimerange"
                        @change="datePickerValueOnChange"
                        @focus="datePickerFocus"
                        @blur="datePickerBlur"
                        :picker-options="pickerOptions"
                        placeholder="Select date and time"
                    >
                    </el-date-picker>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="onSubmit">Query</el-button>
                </el-form-item>
                <popconfirm
                    icon="el-icon-info"
                    v-on:on-confirm="truncateall"
                    title="Are you sure to clear all?"
                >
                    <el-button slot="reference" type="danger">CLEAR ALL</el-button>
                </popconfirm>
            </el-form>

            <div>
                <template v-for="(project, index) in projects">
                    <el-card class="box-card">
                        <div class="operational">
                            <el-badge :value="projectLineCharts[project.project_name][project.sequenceSelected].sum" :max="99999999" class="item" type="primary">
                                <el-select :value="project.sequenceSelected" @change="value => onSelectSequence(value, index)" filterable placeholder="Select">
                                    <el-option
                                        v-for="sequence in project.sequences"
                                        :key="sequence"
                                        :label="sequence"
                                        :value="sequence">
                                    </el-option>
                                </el-select>
                            </el-badge>
                            <el-button :id="project.project_name" type="danger" @click.native="deleteProject" icon="el-icon-delete" circle></el-button>
                        </div>
                        <line-chart v-if="project.renderLineChart" :data="projectLineCharts[project.project_name]" :sequence="project.sequenceSelected" :options="project.options"></line-chart>
                    </el-card>
                </template>
            </div>
        </div>
        <div id="default-values" style="display: none">
            {{ json_encode($projects)  }}
        </div>
        <script type="text/javascript" src="{{ mix('js/vendor.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/manifest.js') }}"></script>
        <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
