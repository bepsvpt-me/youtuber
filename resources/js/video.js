const formatNum = (val) => val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');

const ticks = {
  callback: formatNum,
  precision: 0,
};

const zoom = {
  enabled: true,
  mode: 'x',
  speed: 0.05,
  rangeMin: {
    x: labels.length ? Date.parse(labels[0]) : null,
    y: null,
  },
  rangeMax: {
    x: labels.length ? Date.parse(labels[labels.length - 1]) : null,
    y: null,
  },
};

const options = {
  maintainAspectRatio: false,
  tooltips: {
    mode: 'index',
    intersect: false,
    callbacks: {
      label: (tooltipItem, data) => {
        return `${data.datasets[tooltipItem.datasetIndex].label}: ${formatNum(tooltipItem.yLabel)}`;
      },
      afterLabel: (tooltipItem, data) => {
        const idx = tooltipItem.index;
        const values = data.datasets[tooltipItem.datasetIndex].data;

        let inc = '不適用';
        let time = '不適用';

        if (idx > 0) {
          inc = values[idx] - values[idx - 1];
          inc = inc >= 0 ? `+${inc}` : inc;
          time = Date.parse(data.labels[idx].replace(' ', 'T')) - Date.parse(data.labels[idx - 1].replace(' ', 'T'));
          time = `${(time / 1000 / 60).toFixed(0)} 分鐘`;
        }

        return `增長數: ${inc}\n時間差: ${time}`;
      }
    },
  },
  scales: {
    xAxes: [{
      type: 'time',
      distribution: 'linear',
      time: {
        displayFormats: {
          minute: 'MM/DD HH:mm',
          hour: 'MM/DD HH:00'
        },
        unit: unit,
      },
      ticks: {
        autoSkip: true,
      },
    }],
    yAxes: [{ ticks }],
  },
  plugins: {
    zoom: {
      pan: zoom,
      zoom: zoom,
    },
  },
};

const style = {
  backgroundColor: 'rgba(54, 162, 235, .2)',
  borderColor: 'rgb(54, 162, 235)',
  borderWidth: 2,
  fill: false,
  pointRadius: 0,
};

new Chart(document.querySelector('#views').getContext('2d'), {
  type: 'line',
  data: {
    labels,
    datasets: [{
      label: '觀看數',
      data: views,
      ...style,
    }],
  },
  options,
});

new Chart(document.querySelector('#comments').getContext('2d'), {
  type: 'line',
  data: {
    labels,
    datasets: [{
      label: '留言數',
      data: comments,
      ...style,
    }],
  },
  options,
});

new Chart(document.querySelector('#likes').getContext('2d'), {
  type: 'line',
  data: {
    labels,
    datasets: [{
      label: '喜歡數',
      data: likes,
      ...style,
    }, {
      label: '不喜歡數',
      data: dislikes,
      yAxisID: 'y-dislikes',
      ...Object.assign(style, {
        backgroundColor: 'rgba(235,97,26,0.2)',
        borderColor: 'rgb(235,96,25)',
      }),
    }],
  },
  options: Object.assign(options, Object.assign(options.scales, Object.assign(options.scales.yAxes, {
    yAxes: [{
      ticks,
    }, {
      position: 'right',
      id: 'y-dislikes',
      ticks,
    }],
  }))),
});
