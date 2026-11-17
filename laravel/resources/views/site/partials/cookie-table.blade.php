<table class="cc-table">
  <thead>
    <tr><th scope="col">Názov</th><th scope="col">Poskytovateľ</th><th scope="col">Účel</th><th scope="col">Platnosť</th></tr>
  </thead>
  <tbody>
    @foreach ($cookies as $cookie)
      <tr>
        <td><code>{{ $cookie['name'] }}</code></td>
        <td>{{ $cookie['provider'] }}</td>
        <td>{{ $cookie['purpose'] }}</td>
        <td>{{ $cookie['duration'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
