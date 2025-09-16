// Courses Page - Search and Filter Functionality
class CourseSearch {
  constructor() {
    this.searchInput = document.getElementById('courseSearch');
    this.searchBtn = document.getElementById('searchBtn');
    this.suggestionsContainer = document.getElementById('searchSuggestions');
    this.coursesGrid = document.getElementById('coursesGrid');
    this.courses = this.extractCourseData();
    
    this.init();
  }

  init() {
    this.attachEventListeners();
    this.setupFilters();
  }

  // Extract course data from the DOM
  extractCourseData() {
    const courseCards = document.querySelectorAll('.course-card');
    return Array.from(courseCards).map(card => {
      const title = card.querySelector('h3').textContent.trim();
      const description = card.querySelector('p').textContent.trim();
      const level = card.getAttribute('data-level');
      const instructor = card.querySelector('.fas.fa-user').nextElementSibling.textContent.trim();
      
      return {
        element: card,
        title: title.toLowerCase(),
        description: description.toLowerCase(),
        level: level,
        instructor: instructor.toLowerCase(),
        searchText: `${title} ${description} ${level} ${instructor}`.toLowerCase()
      };
    });
  }

  attachEventListeners() {
    // Search input events
    this.searchInput.addEventListener('input', this.handleSearchInput.bind(this));
    this.searchInput.addEventListener('focus', this.handleSearchFocus.bind(this));
    this.searchInput.addEventListener('blur', this.handleSearchBlur.bind(this));
    this.searchInput.addEventListener('keydown', this.handleKeyDown.bind(this));

    // Search button click
    this.searchBtn.addEventListener('click', this.handleSearchClick.bind(this));

    // Close suggestions when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.search-bar-wrapper')) {
        this.hideSuggestions();
      }
    });
  }

  handleSearchInput(e) {
    const query = e.target.value.trim();
    
    if (query.length > 0) {
      this.showSuggestions(query);
      this.filterCourses(query);
    } else {
      this.hideSuggestions();
      this.showAllCourses();
    }
  }

  handleSearchFocus() {
    const query = this.searchInput.value.trim();
    if (query.length > 0) {
      this.showSuggestions(query);
    }
  }

  handleSearchBlur() {
    // Delay hiding to allow clicking on suggestions
    setTimeout(() => {
      this.hideSuggestions();
    }, 200);
  }

  handleKeyDown(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      this.handleSearchClick();
    }
  }

  handleSearchClick() {
    const query = this.searchInput.value.trim();
    this.filterCourses(query);
    this.hideSuggestions();
    
    // Add visual feedback
    this.searchBtn.style.transform = 'scale(0.95)';
    setTimeout(() => {
      this.searchBtn.style.transform = '';
    }, 150);

    // Show search results message
    this.showSearchResultsMessage(query);
  }

  showSuggestions(query) {
    const suggestions = this.generateSuggestions(query);
    
    if (suggestions.length > 0) {
      this.suggestionsContainer.innerHTML = suggestions
        .slice(0, 5) // Limit to 5 suggestions
        .map(suggestion => `
          <div class="search-suggestion-item" data-suggestion="${suggestion}">
            ${this.highlightMatch(suggestion, query)}
          </div>
        `).join('');
      
      // Add click listeners to suggestions
      this.suggestionsContainer.querySelectorAll('.search-suggestion-item').forEach(item => {
        item.addEventListener('click', () => {
          this.searchInput.value = item.getAttribute('data-suggestion');
          this.filterCourses(item.getAttribute('data-suggestion'));
          this.hideSuggestions();
        });
      });
      
      this.suggestionsContainer.classList.add('show');
    } else {
      this.hideSuggestions();
    }
  }

  hideSuggestions() {
    this.suggestionsContainer.classList.remove('show');
  }

  generateSuggestions(query) {
    const suggestions = new Set();
    const queryLower = query.toLowerCase();
    
    this.courses.forEach(course => {
      // Add course titles that match
      if (course.title.includes(queryLower)) {
        suggestions.add(course.title);
      }
      
      // Add relevant keywords
      const keywords = ['javascript', 'web development', 'ui/ux', 'design', 'programming', 'frontend', 'backend', 'full stack'];
      keywords.forEach(keyword => {
        if (keyword.includes(queryLower) && queryLower.length > 1) {
          suggestions.add(keyword);
        }
      });
    });
    
    return Array.from(suggestions);
  }

  highlightMatch(text, query) {
    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<strong>$1</strong>');
  }

  filterCourses(query) {
    const queryLower = query.toLowerCase();
    let visibleCount = 0;
    
    this.courses.forEach(course => {
      const matches = course.searchText.includes(queryLower);
      
      if (matches || query === '') {
        course.element.style.display = 'block';
        course.element.style.animation = 'fadeInUp 0.5s ease forwards';
        visibleCount++;
      } else {
        course.element.style.display = 'none';
      }
    });

    // Update the courses grid layout
    this.updateGridLayout(visibleCount);
  }

  showAllCourses() {
    this.courses.forEach(course => {
      course.element.style.display = 'block';
      course.element.style.animation = 'fadeInUp 0.5s ease forwards';
    });
    this.updateGridLayout(this.courses.length);
  }

  updateGridLayout(visibleCount) {
    if (visibleCount === 0) {
      this.showNoResultsMessage();
    } else {
      this.hideNoResultsMessage();
    }
  }

  showSearchResultsMessage(query) {
    const existingMessage = document.querySelector('.search-results-message');
    if (existingMessage) {
      existingMessage.remove();
    }

    if (query) {
      const visibleCourses = this.courses.filter(course => 
        course.element.style.display !== 'none'
      ).length;

      const message = document.createElement('div');
      message.className = 'search-results-message';
      message.innerHTML = `
        <p style="color: rgba(255,255,255,0.9); text-align: center; margin: 1rem 0;">
          Found ${visibleCourses} course${visibleCourses !== 1 ? 's' : ''} for "${query}"
        </p>
      `;
      
      this.coursesGrid.parentElement.insertBefore(message, this.coursesGrid);
    }
  }

  showNoResultsMessage() {
    const existingMessage = document.querySelector('.no-results-message');
    if (existingMessage) return;

    const message = document.createElement('div');
    message.className = 'no-results-message';
    message.innerHTML = `
      <div style="text-align: center; padding: 3rem; color: rgba(255,255,255,0.8);">
        <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
        <h3 style="margin-bottom: 1rem;">No courses found</h3>
        <p>Try adjusting your search terms or browse all courses.</p>
        <button onclick="courseSearch.clearSearch()" style="margin-top: 1rem; padding: 0.8rem 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 25px; color: white; cursor: pointer;">
          Clear Search
        </button>
      </div>
    `;
    
    this.coursesGrid.appendChild(message);
  }

  hideNoResultsMessage() {
    const message = document.querySelector('.no-results-message');
    if (message) {
      message.remove();
    }
  }

  clearSearch() {
    this.searchInput.value = '';
    this.showAllCourses();
    this.hideSuggestions();
    this.hideNoResultsMessage();
    
    const searchMessage = document.querySelector('.search-results-message');
    if (searchMessage) {
      searchMessage.remove();
    }
  }

  setupFilters() {
    // Setup existing filter functionality if needed
    const categoryFilter = document.getElementById('categoryFilter');
    const levelFilter = document.getElementById('levelFilter');
    const priceFilter = document.getElementById('priceFilter');

    [categoryFilter, levelFilter, priceFilter].forEach(filter => {
      if (filter) {
        filter.addEventListener('change', () => {
          this.applyFilters();
        });
      }
    });
  }

  applyFilters() {
    const query = this.searchInput.value.trim();
    this.filterCourses(query);
  }
}

// CSS animation keyframes for course cards
const style = document.createElement('style');
style.textContent = `
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .course-card {
    animation-fill-mode: both;
  }
`;
document.head.appendChild(style);

// Initialize search functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  window.courseSearch = new CourseSearch();
  
  // Add some visual enhancements
  const searchInput = document.getElementById('courseSearch');
  if (searchInput) {
    // Add loading state for search
    searchInput.addEventListener('input', function() {
      if (this.value.length > 2) {
        this.style.background = 'rgba(255, 255, 255, 0.15)';
      } else {
        this.style.background = 'rgba(255, 255, 255, 0.1)';
      }
    });
  }
});

// Export for global access
window.CourseSearch = CourseSearch;