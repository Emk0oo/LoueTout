import { startStimulusApp } from '@symfony/stimulus-bundle';
import flatpickr_controller from './controllers/flatpickr_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
app.register('flatpickr_controller', flatpickr_controller);
