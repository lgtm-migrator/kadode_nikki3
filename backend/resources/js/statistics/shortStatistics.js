import {
    Chart,
    LineController,
    LineElement,
    BarElement,
    PointElement,
    ArcElement,
    PieController,
    CategoryScale,
    BarController,
    LinearScale,
    Legend,
} from "chart.js";
import annotationPlugin from "chartjs-plugin-annotation";
Chart.register(
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    LineController,
    BarController,
    CategoryScale,
    PieController,
    LinearScale,
    Legend,
    annotationPlugin
);
window.Chart = Chart;
