import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        addLabel: String,
        deleteLabel: String
    }

    connect() {
        this.index = this.element.childElementCount;
        const btn = document.createElement('button');
        btn.setAttribute('class', 'inline-flex items-center gap-2 w-full rounded-full border-2 border-pink-500 bg-pink-500 p-2 text-white hover:bg-white hover:text-pink-500 hover:border-pink-500 focus:outline-none focus:ring active:text-pink-500');
        btn.setAttribute('type', 'button');
        btn.innerHTML = `
            <span class="text-center w-full">Add bet</span>`;
        btn.addEventListener('click', this.addElement);
        this.element.childNodes.forEach(this.addDeleteButton);
        this.element.append(btn);
    }

    addElement = (e) => {
        e.preventDefault();
        const element = document.createRange().createContextualFragment(
            this.element.dataset['prototype'].replaceAll('__name__', this.index)
        ).firstElementChild;
        this.addDeleteButton(element);
        this.index++;
        e.currentTarget.insertAdjacentElement('beforebegin', element);
    }

    addDeleteButton = (item) => {
        const btn = document.createElement('button');
        btn.setAttribute('class', 'inline-flex items-center gap-2 w-full rounded-full border-2 border-pink-500 bg-pink-500 p-2 text-white hover:bg-white hover:text-pink-500 hover:border-pink-500 focus:outline-none focus:ring active:text-pink-500');
        btn.setAttribute('type', 'button');
        btn.innerHTML = `
            <span class="text-center w-full">Remove bet</span>`;
        item.append(btn);
        btn.addEventListener('click', e => {
            e.preventDefault();
            item.remove();
        });
    }
}
