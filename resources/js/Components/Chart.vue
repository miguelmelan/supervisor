<script setup>
import { ref, onBeforeMount, computed } from 'vue';
import _ from 'lodash';

const props = defineProps({
    id: String,
    type: String,
    direction: {
        type: String,
        default: 'vertical',
    },
    data: Object,
    specificOptions: {
        type: Object,
        default: {},
    },
    width: {
        type: String,
        default: '80',
    },
    height: String,
    innerHorizontalBarTextColor: {
        type: String,
        default: '#595a5c',
    },
    heatmap: {
        type: Object,
        default: {
            colors: [],
            categories: [],
            series: [],
            tooltip: {
                backgroundColor: '',
            },
            height: '',
        }
    },
    showLegend: {
        type: Boolean,
        default: true,
    },
});

const options = ref({
    plugins: {
        htmlLegend: {
            containerID: `legend-container-${props.id}`,
        },
        innerHorizontalBarText: {},
        legend: {
            display: false,
        },
        tooltip: {
            enabled: false,
            position: 'nearest',
            external: (context) => {
                // Tooltip Element
                const { chart, tooltip } = context;
                const tooltipEl = getOrCreateTooltip(chart);

                // Hide if no tooltip
                if (tooltip.opacity === 0) {
                    tooltipEl.style.opacity = 0;
                    return;
                }

                // Set Text
                if (tooltip.body) {
                    const titleLines = tooltip.title || [];
                    const bodyLines = tooltip.body.map(b => b.lines);

                    const tableHead = document.createElement('thead');

                    titleLines.forEach(title => {
                        const tr = document.createElement('tr');
                        tr.style.borderWidth = 0;

                        const th = document.createElement('th');
                        th.style.borderWidth = 0;
                        const text = document.createTextNode(title);

                        th.appendChild(text);
                        tr.appendChild(th);
                        tableHead.appendChild(tr);
                    });

                    const tableBody = document.createElement('tbody');
                    bodyLines.forEach((body, i) => {
                        const currentItem = tooltip.dataPoints[i];
                        const colors = tooltip.labelColors[i];

                        const span = document.createElement('span');
                        span.classList.add('h-3.5', 'w-3.5', 'mr-2', 'rounded-full', 'block');
                        span.style.background = colors.backgroundColor;
                        span.style.borderColor = colors.borderColor;

                        const tr = document.createElement('tr');
                        tr.style.backgroundColor = 'inherit';
                        tr.style.borderWidth = 0;

                        const td = document.createElement('td');
                        td.style.borderWidth = 0;

                        const value = document.createElement('strong');
                        value.innerText = currentItem.formattedValue;

                        const title = document.createElement('div');
                        title.classList.add('flex', 'justify-center', 'items-center', 'text-xl');
                        title.appendChild(span);
                        title.appendChild(value);
                        td.appendChild(title);

                        if (props.type !== 'bar') {
                            const subtitle = document.createElement('div');
                            subtitle.classList.add('text-sm');
                            subtitle.innerText = currentItem.label;
                            td.appendChild(subtitle);
                        }

                        tr.appendChild(td);
                        tableBody.appendChild(tr);
                    });

                    const tableRoot = tooltipEl.querySelector('table');

                    // Remove old children
                    while (tableRoot.firstChild) {
                        tableRoot.firstChild.remove();
                    }

                    // Add new children
                    tableRoot.appendChild(tableHead);
                    tableRoot.appendChild(tableBody);
                }

                const { offsetLeft: positionX, offsetTop: positionY } = chart.canvas;

                // Display, position, and set styles for font
                tooltipEl.style.opacity = 1;
                tooltipEl.style.left = positionX + tooltip.caretX + 'px';
                tooltipEl.style.top = positionY + tooltip.caretY + 'px';
                tooltipEl.style.font = tooltip.options.bodyFont.string;
                tooltipEl.style.padding = tooltip.options.padding + 'px ' + tooltip.options.padding + 'px';
            },
        },
    },
});

const heatmapOptions = ref({
    chart: {
        id: props.id,
        background: 'transparent',
        fontFamily: 'Noto Sans, sans-serif',
        toolbar: {
            show: false,
        },
    },
    stroke: {
        width: 4,
        colors: [ '#e5e7eb' ],
    },
    plotOptions: {
        heatmap: {
            radius: 2,
            enableShades: true,
            distributed: true,
        },
    },
    dataLabels: {
        enabled: false,
    },
    colors: props.heatmap.colors,
    xaxis: {
        type: 'category',
        categories: props.heatmap.categories,
        axisTicks: {
            show: false,
        },
        axisBorder: {
            show: false,
        },
    },
    yaxis: {
    },
    grid: {
        show: false,
    },
    tooltip: {
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
            const seriesNames = w.config.series.map(serie => serie.name);
            return `<div 
                class="block p-4 text-center bg-white bg-opacity-90 shadow-md rounded-md w-full"
                style="pointer-events: none; transition: all 0.1s ease 0s;">
                <div class="flex justify-center items-center text-xl">
                    <span class="h-3.5 w-3.5 mr-2 rounded-full block ${props.heatmap.tooltip.backgroundColor} border-color-white"></span>
                    <strong>${series[seriesIndex][dataPointIndex]}</strong>
                </div>
                <div class="text-sm">${seriesNames[seriesIndex]} ${props.heatmap.categories[dataPointIndex]}</div>
            </div>`;
        },
    },
});

const plugins = computed(() => {
    let p = [];
    if (props.type === 'bar' && props.direction === 'horizontal') {
        p.push(innerHorizontalBarTextPlugin);
    }
    if (props.showLegend) {
        p.push(htmlLegendPlugin);
    }
    return p;
});

const getOrCreateLegendList = (chart, id) => {
    const legendContainer = document.getElementById(id);
    if (legendContainer) {
        let listContainer = legendContainer.querySelector('ul');

        if (!listContainer) {
            listContainer = document.createElement('ul');
            listContainer.style.display = 'flex';
            listContainer.style.flexDirection = 'row';
            listContainer.style.margin = 0;
            listContainer.style.padding = 0;

            legendContainer.appendChild(listContainer);
        }

        return listContainer;
    }

    return false;
};

const htmlLegendPlugin = {
    id: 'htmlLegend',
    afterUpdate(chart, args, options) {
        const ul = getOrCreateLegendList(chart, options.containerID);
        if (ul) {
            // Remove old legend items
            while (ul.firstChild) {
                ul.firstChild.remove();
            }

            // Reuse the built-in legendItems generator
            const items = chart.options.plugins.legend.labels.generateLabels(chart);

            items.forEach((item, index) => {
                const li = document.createElement('li');

                li.style.alignItems = 'center';
                li.style.cursor = 'pointer';
                li.style.display = 'flex';
                li.style.flexDirection = 'row';
                if (index > 0) {
                    li.classList.add('ml-2');
                }

                li.onclick = () => {
                    const { type } = chart.config;
                    if (type === 'pie' || type === 'doughnut') {
                        // Pie and doughnut charts only have a single dataset and visibility is per item
                        chart.toggleDataVisibility(item.index);
                    } else {
                        chart.setDatasetVisibility(item.datasetIndex, !chart.isDatasetVisible(item.datasetIndex));
                    }
                    chart.update();
                };

                // Color box
                const boxSpan = document.createElement('span');
                boxSpan.style.background = item.fillStyle;
                boxSpan.style.borderColor = item.strokeStyle;
                boxSpan.style.borderWidth = item.lineWidth + 'px';
                boxSpan.style.display = 'inline-block';
                boxSpan.classList.add('h-3.5', 'w-3.5', 'mr-2', 'rounded-full');

                // Text
                const textContainer = document.createElement('p');
                textContainer.style.color = item.fontColor;
                textContainer.classList.add('text-xs');
                textContainer.style.margin = 0;
                textContainer.style.padding = 0;
                textContainer.style.textDecoration = item.hidden ? 'line-through' : '';

                const text = document.createTextNode(item.text);
                textContainer.appendChild(text);

                li.appendChild(boxSpan);
                li.appendChild(textContainer);
                ul.appendChild(li);
            });
        }
    },
};

const innerHorizontalBarTextPlugin = {
    id: 'innerHorizontalBarText',
    afterDatasetsDraw(chart, args, options) {
        const { ctx, data, chartArea: { left }, scales: { x, y } } = chart;
        ctx.save();

        data.labels.forEach((label, index) => {
            ctx.font = 'bolder 14px Noto Sans';
            ctx.fillStyle = props.innerHorizontalBarTextColor;
            ctx.fillText(`${label}`, left + 10, y.getPixelForValue(index) + 4);
        });
    },
}

const getOrCreateTooltip = (chart) => {
    let tooltipEl = chart.canvas.parentNode.querySelector('div');

    if (!tooltipEl) {
        tooltipEl = document.createElement('div');
        tooltipEl.classList.add('block', 'p-4', 'text-center', 'bg-white', 'bg-opacity-90', 'shadow-md', 'rounded-md');
        tooltipEl.style.pointerEvents = 'none';
        tooltipEl.style.position = 'absolute';
        tooltipEl.style.transform = 'translate(-50%, 0)';
        tooltipEl.style.transition = 'all .1s ease';

        const table = document.createElement('table');
        table.style.margin = '0px';

        tooltipEl.appendChild(table);
        chart.canvas.parentNode.appendChild(tooltipEl);
    }

    return tooltipEl;
};

onBeforeMount(() => {
    if (props.type !== 'heatmap') {
        options.value = _.merge(options.value, props.specificOptions);
    }
});
</script>

<template>
    <div class="flex flex-col justify-between h-full">
        <div :class="'w-' + width" class="m-auto">
            <ChartJs v-if="type !== 'heatmap'" :id="id" :type="type" :data="data" :options="options"
                :plugins="plugins" />
            <apexchart v-else :height="heatmap.height" type="heatmap" :options="heatmapOptions" :series="heatmap.series"></apexchart>
        </div>
        <div v-if="props.showLegend" class="mt-4" :id="`legend-container-${id}`"></div>
    </div>
</template>