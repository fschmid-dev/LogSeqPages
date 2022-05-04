import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        if (this.writeValue) {
            this.target = document.body;
        } else {
            this.target = document.documentElement || document.getElementsByTagName('html')[0];
        }
    }

    toggle(event) {
        console.log('toggle');
        event.preventDefault();

        const currentTheme = window.sessionStorage.getItem('theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        window.sessionStorage.setItem('theme', newTheme);

        document.documentElement.classList.remove('theme-' + currentTheme);
        document.documentElement.classList.add('theme-' + newTheme);
    }
}