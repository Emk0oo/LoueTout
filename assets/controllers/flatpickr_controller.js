// assets/controllers/flatpickr_controller.js
import { Controller } from '@hotwired/stimulus';
import '../flatpickr.js';
import '../styles/flatpickr.min.css';  // Importer le style de Flatpickr

console.log('hello');

export default class extends Controller {
    connect() {
        flatpickr(this.element.querySelector('#date-range'), {
            dateFormat: "Y-m-d",
            minDate: "today", 
            locale: {
                firstDayOfWeek: 1
            },
            mode: 'range',
            onChange: (e) => {
                if(e.length === 2) {
                    const startDate = new Date(e[0].getTime() - e[0].getTimezoneOffset() * 60000).toISOString().split('T')[0];
                    const endDate = new Date(e[1].getTime() - e[1].getTimezoneOffset() * 60000).toISOString().split('T')[0];
        
                    this.element.querySelector('.start-date').value = startDate;
                    this.element.querySelector('.end-date').value = endDate;
                }
            }
        });
    }
}
