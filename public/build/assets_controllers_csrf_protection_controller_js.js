"use strict";
(self["webpackChunkk_swap"] = self["webpackChunkk_swap"] || []).push([["assets_controllers_csrf_protection_controller_js"],{

/***/ "./assets/controllers/csrf_protection_controller.js"
/*!**********************************************************!*\
  !*** ./assets/controllers/csrf_protection_controller.js ***!
  \**********************************************************/
(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__),
/* harmony export */   generateCsrfHeaders: () => (/* binding */ generateCsrfHeaders),
/* harmony export */   generateCsrfToken: () => (/* binding */ generateCsrfToken),
/* harmony export */   removeCsrfToken: () => (/* binding */ removeCsrfToken)
/* harmony export */ });
const nameCheck = /^[-_a-zA-Z0-9]{4,22}$/;
const tokenCheck = /^[-_/+a-zA-Z0-9]{24,}$/;

// Generate and double-submit a CSRF token in a form field and a cookie, as defined by Symfony's SameOriginCsrfTokenManager
// Use `form.requestSubmit()` to ensure that the submit event is triggered. Using `form.submit()` will not trigger the event
// and thus this event-listener will not be executed.
document.addEventListener('submit', function (event) {
    generateCsrfToken(event.target);
}, true);

// When @hotwired/turbo handles form submissions, send the CSRF token in a header in addition to a cookie
// The `framework.csrf_protection.check_header` config option needs to be enabled for the header to be checked
document.addEventListener('turbo:submit-start', function (event) {
    const h = generateCsrfHeaders(event.detail.formSubmission.formElement);
    Object.keys(h).map(function (k) {
        event.detail.formSubmission.fetchRequest.headers[k] = h[k];
    });
});

// When @hotwired/turbo handles form submissions, remove the CSRF cookie once a form has been submitted
document.addEventListener('turbo:submit-end', function (event) {
    removeCsrfToken(event.detail.formSubmission.formElement);
});

function generateCsrfToken (formElement) {
    const csrfField = formElement.querySelector('input[data-controller="csrf-protection"], input[name="_csrf_token"]');

    if (!csrfField) {
        return;
    }

    let csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');
    let csrfToken = csrfField.value;

    if (!csrfCookie && nameCheck.test(csrfToken)) {
        csrfField.setAttribute('data-csrf-protection-cookie-value', csrfCookie = csrfToken);
        csrfField.defaultValue = csrfToken = btoa(String.fromCharCode.apply(null, (window.crypto || window.msCrypto).getRandomValues(new Uint8Array(18))));
    }
    csrfField.dispatchEvent(new Event('change', { bubbles: true }));

    if (csrfCookie && tokenCheck.test(csrfToken)) {
        const cookie = csrfCookie + '_' + csrfToken + '=' + csrfCookie + '; path=/; samesite=strict';
        document.cookie = window.location.protocol === 'https:' ? '__Host-' + cookie + '; secure' : cookie;
    }
}

function generateCsrfHeaders (formElement) {
    const headers = {};
    const csrfField = formElement.querySelector('input[data-controller="csrf-protection"], input[name="_csrf_token"]');

    if (!csrfField) {
        return headers;
    }

    const csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');

    if (tokenCheck.test(csrfField.value) && nameCheck.test(csrfCookie)) {
        headers[csrfCookie] = csrfField.value;
    }

    return headers;
}

function removeCsrfToken (formElement) {
    const csrfField = formElement.querySelector('input[data-controller="csrf-protection"], input[name="_csrf_token"]');

    if (!csrfField) {
        return;
    }

    const csrfCookie = csrfField.getAttribute('data-csrf-protection-cookie-value');

    if (tokenCheck.test(csrfField.value) && nameCheck.test(csrfCookie)) {
        const cookie = csrfCookie + '_' + csrfField.value + '=0; path=/; samesite=strict; max-age=0';

        document.cookie = window.location.protocol === 'https:' ? '__Host-' + cookie + '; secure' : cookie;
    }
}

/* stimulusFetch: 'lazy' */
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ('csrf-protection-controller');


/***/ }

}]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXNzZXRzX2NvbnRyb2xsZXJzX2NzcmZfcHJvdGVjdGlvbl9jb250cm9sbGVyX2pzLmpzIiwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7Ozs7QUFBQSxrQ0FBa0MsS0FBSztBQUN2QyxxQ0FBcUMsSUFBSTtBQUN6QztBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQ0Q7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxLQUFLO0FBQ0wsQ0FBQztBQUNEO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsQ0FBQztBQUNEO0FBQ087QUFDUDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGtEQUFrRCxlQUFlO0FBQ2pFO0FBQ0E7QUFDQSw0RUFBNEUsUUFBUTtBQUNwRiwwRkFBMEY7QUFDMUY7QUFDQTtBQUNBO0FBQ087QUFDUDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNPO0FBQ1A7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsaUVBQWlFLFFBQVEsaUJBQWlCO0FBQzFGO0FBQ0EsMEZBQTBGO0FBQzFGO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsaUVBQWUsNEJBQTRCLEVBQUMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9rLXN3YXAvLi9hc3NldHMvY29udHJvbGxlcnMvY3NyZl9wcm90ZWN0aW9uX2NvbnRyb2xsZXIuanMiXSwic291cmNlc0NvbnRlbnQiOlsiY29uc3QgbmFtZUNoZWNrID0gL15bLV9hLXpBLVowLTldezQsMjJ9JC87XHJcbmNvbnN0IHRva2VuQ2hlY2sgPSAvXlstXy8rYS16QS1aMC05XXsyNCx9JC87XHJcblxyXG4vLyBHZW5lcmF0ZSBhbmQgZG91YmxlLXN1Ym1pdCBhIENTUkYgdG9rZW4gaW4gYSBmb3JtIGZpZWxkIGFuZCBhIGNvb2tpZSwgYXMgZGVmaW5lZCBieSBTeW1mb255J3MgU2FtZU9yaWdpbkNzcmZUb2tlbk1hbmFnZXJcclxuLy8gVXNlIGBmb3JtLnJlcXVlc3RTdWJtaXQoKWAgdG8gZW5zdXJlIHRoYXQgdGhlIHN1Ym1pdCBldmVudCBpcyB0cmlnZ2VyZWQuIFVzaW5nIGBmb3JtLnN1Ym1pdCgpYCB3aWxsIG5vdCB0cmlnZ2VyIHRoZSBldmVudFxyXG4vLyBhbmQgdGh1cyB0aGlzIGV2ZW50LWxpc3RlbmVyIHdpbGwgbm90IGJlIGV4ZWN1dGVkLlxyXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdzdWJtaXQnLCBmdW5jdGlvbiAoZXZlbnQpIHtcclxuICAgIGdlbmVyYXRlQ3NyZlRva2VuKGV2ZW50LnRhcmdldCk7XHJcbn0sIHRydWUpO1xyXG5cclxuLy8gV2hlbiBAaG90d2lyZWQvdHVyYm8gaGFuZGxlcyBmb3JtIHN1Ym1pc3Npb25zLCBzZW5kIHRoZSBDU1JGIHRva2VuIGluIGEgaGVhZGVyIGluIGFkZGl0aW9uIHRvIGEgY29va2llXHJcbi8vIFRoZSBgZnJhbWV3b3JrLmNzcmZfcHJvdGVjdGlvbi5jaGVja19oZWFkZXJgIGNvbmZpZyBvcHRpb24gbmVlZHMgdG8gYmUgZW5hYmxlZCBmb3IgdGhlIGhlYWRlciB0byBiZSBjaGVja2VkXHJcbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ3R1cmJvOnN1Ym1pdC1zdGFydCcsIGZ1bmN0aW9uIChldmVudCkge1xyXG4gICAgY29uc3QgaCA9IGdlbmVyYXRlQ3NyZkhlYWRlcnMoZXZlbnQuZGV0YWlsLmZvcm1TdWJtaXNzaW9uLmZvcm1FbGVtZW50KTtcclxuICAgIE9iamVjdC5rZXlzKGgpLm1hcChmdW5jdGlvbiAoaykge1xyXG4gICAgICAgIGV2ZW50LmRldGFpbC5mb3JtU3VibWlzc2lvbi5mZXRjaFJlcXVlc3QuaGVhZGVyc1trXSA9IGhba107XHJcbiAgICB9KTtcclxufSk7XHJcblxyXG4vLyBXaGVuIEBob3R3aXJlZC90dXJibyBoYW5kbGVzIGZvcm0gc3VibWlzc2lvbnMsIHJlbW92ZSB0aGUgQ1NSRiBjb29raWUgb25jZSBhIGZvcm0gaGFzIGJlZW4gc3VibWl0dGVkXHJcbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ3R1cmJvOnN1Ym1pdC1lbmQnLCBmdW5jdGlvbiAoZXZlbnQpIHtcclxuICAgIHJlbW92ZUNzcmZUb2tlbihldmVudC5kZXRhaWwuZm9ybVN1Ym1pc3Npb24uZm9ybUVsZW1lbnQpO1xyXG59KTtcclxuXHJcbmV4cG9ydCBmdW5jdGlvbiBnZW5lcmF0ZUNzcmZUb2tlbiAoZm9ybUVsZW1lbnQpIHtcclxuICAgIGNvbnN0IGNzcmZGaWVsZCA9IGZvcm1FbGVtZW50LnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W2RhdGEtY29udHJvbGxlcj1cImNzcmYtcHJvdGVjdGlvblwiXSwgaW5wdXRbbmFtZT1cIl9jc3JmX3Rva2VuXCJdJyk7XHJcblxyXG4gICAgaWYgKCFjc3JmRmllbGQpIHtcclxuICAgICAgICByZXR1cm47XHJcbiAgICB9XHJcblxyXG4gICAgbGV0IGNzcmZDb29raWUgPSBjc3JmRmllbGQuZ2V0QXR0cmlidXRlKCdkYXRhLWNzcmYtcHJvdGVjdGlvbi1jb29raWUtdmFsdWUnKTtcclxuICAgIGxldCBjc3JmVG9rZW4gPSBjc3JmRmllbGQudmFsdWU7XHJcblxyXG4gICAgaWYgKCFjc3JmQ29va2llICYmIG5hbWVDaGVjay50ZXN0KGNzcmZUb2tlbikpIHtcclxuICAgICAgICBjc3JmRmllbGQuc2V0QXR0cmlidXRlKCdkYXRhLWNzcmYtcHJvdGVjdGlvbi1jb29raWUtdmFsdWUnLCBjc3JmQ29va2llID0gY3NyZlRva2VuKTtcclxuICAgICAgICBjc3JmRmllbGQuZGVmYXVsdFZhbHVlID0gY3NyZlRva2VuID0gYnRvYShTdHJpbmcuZnJvbUNoYXJDb2RlLmFwcGx5KG51bGwsICh3aW5kb3cuY3J5cHRvIHx8IHdpbmRvdy5tc0NyeXB0bykuZ2V0UmFuZG9tVmFsdWVzKG5ldyBVaW50OEFycmF5KDE4KSkpKTtcclxuICAgIH1cclxuICAgIGNzcmZGaWVsZC5kaXNwYXRjaEV2ZW50KG5ldyBFdmVudCgnY2hhbmdlJywgeyBidWJibGVzOiB0cnVlIH0pKTtcclxuXHJcbiAgICBpZiAoY3NyZkNvb2tpZSAmJiB0b2tlbkNoZWNrLnRlc3QoY3NyZlRva2VuKSkge1xyXG4gICAgICAgIGNvbnN0IGNvb2tpZSA9IGNzcmZDb29raWUgKyAnXycgKyBjc3JmVG9rZW4gKyAnPScgKyBjc3JmQ29va2llICsgJzsgcGF0aD0vOyBzYW1lc2l0ZT1zdHJpY3QnO1xyXG4gICAgICAgIGRvY3VtZW50LmNvb2tpZSA9IHdpbmRvdy5sb2NhdGlvbi5wcm90b2NvbCA9PT0gJ2h0dHBzOicgPyAnX19Ib3N0LScgKyBjb29raWUgKyAnOyBzZWN1cmUnIDogY29va2llO1xyXG4gICAgfVxyXG59XHJcblxyXG5leHBvcnQgZnVuY3Rpb24gZ2VuZXJhdGVDc3JmSGVhZGVycyAoZm9ybUVsZW1lbnQpIHtcclxuICAgIGNvbnN0IGhlYWRlcnMgPSB7fTtcclxuICAgIGNvbnN0IGNzcmZGaWVsZCA9IGZvcm1FbGVtZW50LnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W2RhdGEtY29udHJvbGxlcj1cImNzcmYtcHJvdGVjdGlvblwiXSwgaW5wdXRbbmFtZT1cIl9jc3JmX3Rva2VuXCJdJyk7XHJcblxyXG4gICAgaWYgKCFjc3JmRmllbGQpIHtcclxuICAgICAgICByZXR1cm4gaGVhZGVycztcclxuICAgIH1cclxuXHJcbiAgICBjb25zdCBjc3JmQ29va2llID0gY3NyZkZpZWxkLmdldEF0dHJpYnV0ZSgnZGF0YS1jc3JmLXByb3RlY3Rpb24tY29va2llLXZhbHVlJyk7XHJcblxyXG4gICAgaWYgKHRva2VuQ2hlY2sudGVzdChjc3JmRmllbGQudmFsdWUpICYmIG5hbWVDaGVjay50ZXN0KGNzcmZDb29raWUpKSB7XHJcbiAgICAgICAgaGVhZGVyc1tjc3JmQ29va2llXSA9IGNzcmZGaWVsZC52YWx1ZTtcclxuICAgIH1cclxuXHJcbiAgICByZXR1cm4gaGVhZGVycztcclxufVxyXG5cclxuZXhwb3J0IGZ1bmN0aW9uIHJlbW92ZUNzcmZUb2tlbiAoZm9ybUVsZW1lbnQpIHtcclxuICAgIGNvbnN0IGNzcmZGaWVsZCA9IGZvcm1FbGVtZW50LnF1ZXJ5U2VsZWN0b3IoJ2lucHV0W2RhdGEtY29udHJvbGxlcj1cImNzcmYtcHJvdGVjdGlvblwiXSwgaW5wdXRbbmFtZT1cIl9jc3JmX3Rva2VuXCJdJyk7XHJcblxyXG4gICAgaWYgKCFjc3JmRmllbGQpIHtcclxuICAgICAgICByZXR1cm47XHJcbiAgICB9XHJcblxyXG4gICAgY29uc3QgY3NyZkNvb2tpZSA9IGNzcmZGaWVsZC5nZXRBdHRyaWJ1dGUoJ2RhdGEtY3NyZi1wcm90ZWN0aW9uLWNvb2tpZS12YWx1ZScpO1xyXG5cclxuICAgIGlmICh0b2tlbkNoZWNrLnRlc3QoY3NyZkZpZWxkLnZhbHVlKSAmJiBuYW1lQ2hlY2sudGVzdChjc3JmQ29va2llKSkge1xyXG4gICAgICAgIGNvbnN0IGNvb2tpZSA9IGNzcmZDb29raWUgKyAnXycgKyBjc3JmRmllbGQudmFsdWUgKyAnPTA7IHBhdGg9Lzsgc2FtZXNpdGU9c3RyaWN0OyBtYXgtYWdlPTAnO1xyXG5cclxuICAgICAgICBkb2N1bWVudC5jb29raWUgPSB3aW5kb3cubG9jYXRpb24ucHJvdG9jb2wgPT09ICdodHRwczonID8gJ19fSG9zdC0nICsgY29va2llICsgJzsgc2VjdXJlJyA6IGNvb2tpZTtcclxuICAgIH1cclxufVxyXG5cclxuLyogc3RpbXVsdXNGZXRjaDogJ2xhenknICovXHJcbmV4cG9ydCBkZWZhdWx0ICdjc3JmLXByb3RlY3Rpb24tY29udHJvbGxlcic7XHJcbiJdLCJuYW1lcyI6W10sInNvdXJjZVJvb3QiOiIifQ==