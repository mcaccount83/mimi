  <div class="accordion" id="accordion-courses" style="column-count: 2; column-gap: 1rem;">
                        @if(isset($coordinatorCoursesByCategory) && count($coordinatorCoursesByCategory) > 0)
                            @foreach($coordinatorCoursesByCategory as $categorySlug => $categoryData)
                                <div style="break-inside: avoid; margin-bottom: 0.5rem;">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="header-{{ $categorySlug }}">
                                            <button class="accordion-button text-uppercase" type="button"
                            aria-expanded="true">
                        {{ $categoryData['name'] }}
                    </button>
                    </h2>
                    <div id="collapse-{{ $categorySlug }}"
                         class="accordion-collapse">
                        <div class="accordion-body">
                            <ul class="list-unstyled mb-0">
                                @foreach($categoryData['courses'] as $course)
                                    <li class="mb-2 d-flex align-items-center gap-2">
                                        <a href="{{ $course['auto_login_url'] }}" target="_blank">
                                            {{ $course['title']['rendered'] }}
                                        </a>
                                        @if(!empty($course['progress']) && $course['progress']['status'] === 'completed')
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span>
                                        @elseif(!empty($course['progress']) && $course['progress']['status'] === 'in_progress')
                                            <div class="progress mt-2" style="height: 8px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $course['progress']['percent'] }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $course['progress']['percent'] }}% complete ({{ $course['progress']['steps_completed'] }}/{{ $course['progress']['steps_total'] }} steps)</small>
                                        @else
                                            <span class="badge bg-secondary">Not Started</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p>No courses found for your user type.</p>
    @endif
</div>
