(() => {
const root = document.querySelector('[data-thc-detox-calculator]');
if (!root || !window.THCDetoxCalculatorData) {
return;
}

const form = root.querySelector('form');
const steps = Array.from(root.querySelectorAll('.thc-detox-step'));
const nextBtn = root.querySelector('[data-next]');
const prevBtn = root.querySelector('[data-prev]');
const submitBtn = root.querySelector('[data-submit]');
const errorsBox = root.querySelector('.thc-detox-calculator__errors');
const resultsBox = root.querySelector('[data-results]');
let currentStep = 0;

const fieldsetsByStep = [
['gender', 'age', 'weight', 'weight_unit'],
['body_rhythm', 'movement'],
['frequency', 'quantity', 'potency'],
['last_use'],
[],
];

	const showErrors = (messages) => {
		if (!messages || !messages.length) {
			errorsBox.hidden = true;
			errorsBox.textContent = '';
			return;
		}
		errorsBox.hidden = false;
		const list = document.createElement('ul');
		messages.forEach((message) => {
			const item = document.createElement('li');
			item.textContent = String(message);
			list.appendChild(item);
		});
		errorsBox.textContent = '';
		errorsBox.appendChild(list);
	};

const setStep = (index) => {
currentStep = Math.max(0, Math.min(index, steps.length - 1));
steps.forEach((step, idx) => step.classList.toggle('is-active', idx === currentStep));
prevBtn.disabled = currentStep === 0;

const lastInputStep = steps.length - 2;
nextBtn.hidden = currentStep >= lastInputStep;
submitBtn.hidden = currentStep !== lastInputStep;
};

const getField = (name) => form.querySelector(`[name="${name}"]`);

const collectValues = () => {
const fd = new FormData(form);
const payload = new URLSearchParams();
fd.forEach((value, key) => payload.append(key, String(value)));
payload.append('action', 'thc_detox_calculate');
payload.append('nonce', THCDetoxCalculatorData.nonce);
return payload;
};

const validateStep = () => {
const names = fieldsetsByStep[currentStep] || [];
const errors = [];
const today = new Date().toISOString().slice(0, 10);

names.forEach((name) => {
if (name === 'movement') {
const selected = form.querySelector('input[name="movement"]:checked');
if (!selected) {
errors.push('Selecciona tu movimiento semanal.');
}
return;
}

const field = getField(name);
if (!field) {
return;
}

const value = String(field.value || '').trim();
if (!value) {
errors.push(`Completa el campo "${field.closest('label')?.querySelector('span')?.textContent || name}".`);
return;
}

if (name === 'age') {
const age = Number(value);
if (Number.isNaN(age) || age < 18 || age > 122) {
errors.push('La edad debe estar entre 18 y 122 años.');
}
}

if (name === 'weight') {
const weight = Number(value);
const unit = String(getField('weight_unit')?.value || 'kg');
if (Number.isNaN(weight)) {
errors.push('Introduce un peso válido.');
} else if (unit === 'kg' && (weight < 30 || weight > 240)) {
errors.push('Si usas kg, el peso debe estar entre 30 y 240.');
} else if (unit === 'lb' && (weight < 66 || weight > 529)) {
errors.push('Si usas lb, el peso debe estar entre 66 y 529.');
}
}

if (name === 'last_use' && value > today) {
errors.push('La fecha de última toma no puede estar en el futuro.');
}
});

showErrors(errors);
return !errors.length;
};

const makeCalendarButtons = (calendar) => {
const wrap = document.createElement('div');
wrap.className = 'thc-card__actions';

const google = document.createElement('a');
google.className = 'thc-btn thc-btn--small';
google.href = calendar.google;
google.target = '_blank';
google.rel = 'noopener noreferrer';
google.textContent = 'Añadir a Google Calendar';
wrap.appendChild(google);

const ics = document.createElement('a');
ics.className = 'thc-btn thc-btn--small thc-btn--ghost';
ics.textContent = 'Descargar .ics';
ics.href = `data:text/calendar;charset=utf-8;base64,${calendar.ics}`;
ics.download = calendar.ics_name;
wrap.appendChild(ics);

return wrap;
};

	const renderCard = (data, testKey) => {
		const card = document.createElement('article');
		card.className = 'thc-result-card';
		const title = document.createElement('h4');
		title.textContent = String(data.label || '');
		card.appendChild(title);

		const range = document.createElement('p');
		range.className = 'thc-result-card__range';
		range.textContent = String(data.window_readable || '');
		card.appendChild(range);

		const list = document.createElement('ul');
		list.className = 'thc-result-card__dates';
		[
			['Optimista', data?.dates?.optimistic?.label],
			['Probable', data?.dates?.probable?.label],
			['Conservadora', data?.dates?.conservative?.label],
		].forEach(([label, dateLabel]) => {
			const li = document.createElement('li');
			const strong = document.createElement('strong');
			strong.textContent = `${label}: `;
			li.appendChild(strong);
			li.appendChild(document.createTextNode(String(dateLabel || '')));
			list.appendChild(li);
		});
		card.appendChild(list);

		const target = document.createElement('p');
		target.className = 'thc-result-card__target';
		target.appendChild(document.createTextNode(`Fecha estimada (${testKey}): `));
		const strong = document.createElement('strong');
		strong.textContent = String(data?.dates?.conservative?.label || '');
		target.appendChild(strong);
		card.appendChild(target);

		card.appendChild(makeCalendarButtons(data.calendar));
		return card;
	};

const renderResults = (payload) => {
resultsBox.hidden = false;
resultsBox.innerHTML = '';

const disclaimer = document.createElement('p');
disclaimer.className = 'thc-detox-calculator__alert thc-detox-calculator__alert--soft';
disclaimer.textContent = payload.disclaimer;
resultsBox.appendChild(disclaimer);

const cards = document.createElement('div');
cards.className = 'thc-results-grid';
cards.appendChild(renderCard(payload.urine, 'orina'));
cards.appendChild(renderCard(payload.blood, 'sangre'));
resultsBox.appendChild(cards);

		if (payload.influences?.length) {
			const influences = document.createElement('div');
			influences.className = 'thc-detox-influences';
			const title = document.createElement('h4');
			title.textContent = 'Factores con más impacto';
			influences.appendChild(title);
			const list = document.createElement('ul');
			payload.influences.forEach((line) => {
				const li = document.createElement('li');
				li.textContent = String(line);
				list.appendChild(li);
			});
			influences.appendChild(list);
			resultsBox.appendChild(influences);
		}
	};

nextBtn.addEventListener('click', () => {
if (!validateStep()) {
return;
}
showErrors([]);
setStep(currentStep + 1);
});

prevBtn.addEventListener('click', () => {
showErrors([]);
setStep(currentStep - 1);
});

getField('weight_unit')?.addEventListener('change', (event) => {
const unit = String(event.target.value || 'kg');
const weight = getField('weight');
if (!weight) {
return;
}
if (unit === 'lb') {
weight.min = '66';
weight.max = '529';
} else {
weight.min = '30';
weight.max = '240';
}
});

form.addEventListener('submit', async (event) => {
event.preventDefault();
if (!validateStep()) {
return;
}

submitBtn.disabled = true;
submitBtn.textContent = String(submitBtn.dataset.loadingText || 'Calculando...');
showErrors([]);

try {
const response = await fetch(THCDetoxCalculatorData.ajaxUrl, {
method: 'POST',
headers: {
'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
},
body: collectValues().toString(),
});
const json = await response.json();
if (!json.success) {
showErrors(json?.data?.errors || ['No fue posible procesar la estimación.']);
return;
}

renderResults(json.data);
setStep(steps.length - 1);
} catch (error) {
showErrors(['Se produjo un error al calcular. Inténtalo de nuevo.']);
} finally {
submitBtn.disabled = false;
submitBtn.textContent = String(submitBtn.dataset.defaultText || 'Calcular ventana estimada');
}
});

setStep(0);
})();
