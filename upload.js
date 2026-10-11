document.querySelectorAll('.dz').forEach((dz) => {
  const input = dz.querySelector('input[type="file"]');
  const text  = dz.querySelector('.dz__text');

  input.addEventListener('change', () => {
    if (input.files && input.files[0]) {
      text.textContent = input.files[0].name;
      dz.classList.add('filled');
    }
  });

  ['dragenter','dragover'].forEach(evt =>
    dz.addEventListener(evt, (e) => {
      e.preventDefault(); e.stopPropagation();
      dz.classList.add('is-dragover');
    })
  );
  ['dragleave','dragend','drop'].forEach(evt =>
    dz.addEventListener(evt, (e) => {
      e.preventDefault(); e.stopPropagation();
      dz.classList.remove('is-dragover');
    })
  );
  dz.addEventListener('drop', (e) => {
    const files = e.dataTransfer.files;
    if (files && files[0]) {
      input.files = files;             
      text.textContent = files[0].name; 
      dz.classList.add('filled');
    }
  });
});

document.getElementById('masa_berlaku').addEventListener('input', function() {
    const input = this.value.trim();
    const statusDiv = document.getElementById('status-uji');

    if (!input) {
        statusDiv.textContent = '';
        statusDiv.className = 'status-line';
        return;
    }

    const parts = input.split('-');
    if (parts.length !== 3) {
        statusDiv.textContent = 'Format tanggal tidak valid';
        statusDiv.className = 'status-line status-expired';
        return;
    }

    const day = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10) - 1; 
    const year = parseInt(parts[2], 10);

    const expiryDate = new Date(year, month, day);
    if (isNaN(expiryDate.getTime())) {
        statusDiv.textContent = 'Format tanggal tidak valid';
        statusDiv.className = 'status-line status-expired';
        return;
    }

    const today = new Date();
    today.setHours(0,0,0,0); 

    if (expiryDate >= today) {
        statusDiv.textContent = 'Hasil Uji Masih Berlaku';
        statusDiv.className = 'status-line status-valid';
    } else {
        statusDiv.textContent = 'Hasil Uji Sudah Tidak Berlaku';
        statusDiv.className = 'status-line status-expired';
    }
});
