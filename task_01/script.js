const form = document.getElementById('studentForm');
const tbody = document.querySelector('#studentTable tbody');

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const data = {
    name: name.value,
    email: email.value,
    dob: dob.value,
    department: department.value,
    phone: phone.value
  };

  await fetch('/students', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  });

  form.reset();
  loadStudents();
});

async function loadStudents() {
  const res = await fetch('/students');
  const students = await res.json();
  tbody.innerHTML = students.map(s => `
    <tr>
      <td>${s.name}</td><td>${s.email}</td><td>${s.dob}</td><td>${s.department}</td><td>${s.phone}</td>
    </tr>
  `).join('');
}
loadStudents();
