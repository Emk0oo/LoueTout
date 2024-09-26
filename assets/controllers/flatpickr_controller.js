// assets/controllers/flatpickr_controller.js
import { Controller } from 'stimulus';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';  // Importer le style de Flatpickr

export default class extends Controller {
    connect() {
        flatpickr(this.element.querySelector('.start-date'), {
            dateFormat: "Y-m-d",
            minDate: "today", 
            locale: {
                firstDayOfWeek: 1
            },
        });

        flatpickr(this.element.querySelector('.end-date'), {
            dateFormat: "Y-m-d",
            minDate: "today",
            locale: {
                firstDayOfWeek: 1 
            },
        });
    }
}
