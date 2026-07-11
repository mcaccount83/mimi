@props(['text'])

<i class="fas fa-info-circle text-muted ms-1 help-icon"
   style="cursor: pointer; font-size: 0.85em;"
   role="button"
   tabindex="0"
   aria-label="How this is calculated"
   onclick="Swal.fire({
       icon: 'info',
       title: 'How this is calculated',
       text: @js($text),
       confirmButtonColor: '#007bff'
   })"></i>
