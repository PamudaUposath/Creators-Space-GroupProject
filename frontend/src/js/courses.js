// Courses Page - Search and Filter Functionality
class CourseSearch {
  constructor() {
    this.searchInput = document.getElementById('courseSearch');
    this.searchBtn = document.getElementById('searchBtn');
    this.suggestionsContainer = document.getElementById('searchSuggestions');
    this.coursesGrid = document.getElementById('coursesGrid');
    this.courses = this.extractCourseData();
    this.config = this.getSearchConfiguration();
    
    this.init();
  }

  // Search configuration object for better maintainability
  getSearchConfiguration() {
    return {
      // Keywords for search suggestions - easily extensible
      keywords: [
        // Programming Languages
        'javascript', 'python', 'java', 'typescript', 'php', 'c++', 'c#', 'go', 'rust',
        
        // Web Development
        'web development', 'frontend', 'backend', 'full stack', 'fullstack',
        'html', 'css', 'react', 'angular', 'vue', 'node.js', 'express',
        
        // Design & UI/UX
        'ui/ux', 'design', 'user interface', 'user experience', 'figma', 'photoshop',
        'graphic design', 'web design', 'responsive design',
        
        // Data & Analytics
        'data science', 'machine learning', 'artificial intelligence', 'ai', 'data analysis',
        'statistics', 'big data', 'analytics',
        
        // Mobile Development
        'mobile development', 'ios', 'android', 'react native', 'flutter', 'swift', 'kotlin',
        
        // DevOps & Infrastructure
        'devops', 'docker', 'kubernetes', 'aws', 'azure', 'cloud computing', 'ci/cd',
        
        // Databases
        'database', 'sql', 'mysql', 'postgresql', 'mongodb', 'redis',
        
        // General Programming Concepts
        'programming', 'coding', 'software development', 'algorithms', 'data structures',
        'api', 'rest', 'graphql', 'microservices'
      ],
      
      // Search options
      maxSuggestions: 5,
      minQueryLength: 1,
      searchDelay: 300, // milliseconds
      
      // Categories for filtering (can be extended)
      categories: [
        'programming', 'design', 'data-science', 'mobile', 'web-development'
      ],
      
      // Levels for filtering
      levels: ['beginner', 'intermediate', 'advanced']
    };
  }

  // Method to add custom keywords dynamically
  addKeywords(newKeywords) {
    if (Array.isArray(newKeywords)) {
      this.config.keywords.push(...newKeywords);
      // Remove duplicates
      this.config.keywords = [...new Set(this.config.keywords)];
    }
  }

  // Method to remove keywords
  removeKeywords(keywordsToRemove) {
    if (Array.isArray(keywordsToRemove)) {
      this.config.keywords = this.config.keywords.filter(
        keyword => !keywordsToRemove.includes(keyword)
      );
    }
  }

  // Method to update search configuration
  updateConfig(newConfig) {
    this.config = { ...this.config, ...newConfig };
  }

  // Method to get keywords from course content (for auto-generation)
  extractKeywordsFromCourses() {
    const extractedKeywords = new Set();
    
    this.courses.forEach(course => {
      // Extract common words from titles and descriptions
      const words = course.searchText.split(/\s+/)
        .filter(word => word.length > 3) // Only words longer than 3 characters
        .map(word => word.toLowerCase())
        .filter(word => /^[a-zA-Z]+$/.test(word)); // Only alphabetic words
      
      words.forEach(word => extractedKeywords.add(word));
    });
    
    return Array.from(extractedKeywords);
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
      this.applyFilters();
    } else {
      this.hideSuggestions();
      this.applyFilters(); // Apply filters even with empty search to respect dropdown filters
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
    this.applyFilters();
    this.hideSuggestions();
    
    // Add visual feedback
    this.searchBtn.style.transform = 'scale(0.95)';
    setTimeout(() => {
      this.searchBtn.style.transform = '';
    }, 150);
  }

  showSuggestions(query) {
    const suggestions = this.generateSuggestions(query);
    
    if (suggestions.length > 0) {
      this.suggestionsContainer.innerHTML = suggestions
        .slice(0, this.config.maxSuggestions) // Use configurable max suggestions
        .map(suggestion => `
          <div class="search-suggestion-item" data-suggestion="${suggestion}">
            ${this.highlightMatch(suggestion, query)}
          </div>
        `).join('');
      
      // Add click listeners to suggestions
      this.suggestionsContainer.querySelectorAll('.search-suggestion-item').forEach(item => {
        item.addEventListener('click', () => {
          this.searchInput.value = item.getAttribute('data-suggestion');
          this.applyFilters();
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
    });
    
    // Add relevant keywords from configuration
    this.config.keywords.forEach(keyword => {
      if (keyword.includes(queryLower) && queryLower.length >= this.config.minQueryLength) {
        suggestions.add(keyword);
      }
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
    const searchTerm = this.searchInput ? this.searchInput.value.toLowerCase() : '';
    const levelFilter = document.getElementById('levelFilter')?.value || '';
    const categoryFilter = document.getElementById('categoryFilter')?.value || '';
    const priceFilter = document.getElementById('priceFilter')?.value || '';
    
    let visibleCount = 0;

    this.courses.forEach(course => {
      // Get data attributes
      const level = course.element.getAttribute('data-level');
      const category = course.element.getAttribute('data-category');
      const priceType = course.element.getAttribute('data-price');

      // Check all filter conditions
      const matchesSearch = !searchTerm || course.searchText.includes(searchTerm);
      const matchesLevel = !levelFilter || level === levelFilter;
      const matchesCategory = !categoryFilter || category === categoryFilter;
      const matchesPrice = !priceFilter || priceType === priceFilter;

      // Show/hide course based on all filters
      if (matchesSearch && matchesLevel && matchesCategory && matchesPrice) {
        course.element.style.display = 'block';
        course.element.style.animation = 'fadeInUp 0.5s ease forwards';
        visibleCount++;
      } else {
        course.element.style.display = 'none';
      }
    });
    
    // Update results display
    this.updateFilterResults(visibleCount, searchTerm, levelFilter, categoryFilter, priceFilter);
  }

  updateFilterResults(visibleCount, searchTerm, levelFilter, categoryFilter, priceFilter) {
    // Remove existing messages
    const existingMessage = document.querySelector('.filter-results-message');
    if (existingMessage) {
      existingMessage.remove();
    }
    
    const coursesGrid = this.coursesGrid;
    
    if (visibleCount === 0) {
      // Show no results message
      const message = document.createElement('div');
      message.className = 'filter-results-message';
      message.innerHTML = `
        <div style="text-align: center; padding: 3rem; color: rgba(255,255,255,0.8); grid-column: 1 / -1;">
          <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
          <h3 style="margin-bottom: 1rem; color: #ffffff;">No courses found</h3>
          <p style="margin-bottom: 1.5rem;">No courses match your current filters. Try adjusting your search criteria.</p>
          <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-bottom: 1rem;">
            ${searchTerm ? `<span style="background: rgba(102, 126, 234, 0.2); padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.9rem;">Search: "${searchTerm}"</span>` : ''}
            ${levelFilter ? `<span style="background: rgba(102, 126, 234, 0.2); padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.9rem;">Level: ${levelFilter}</span>` : ''}
            ${categoryFilter ? `<span style="background: rgba(102, 126, 234, 0.2); padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.9rem;">Category: ${categoryFilter}</span>` : ''}
            ${priceFilter ? `<span style="background: rgba(102, 126, 234, 0.2); padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.9rem;">Price: ${priceFilter}</span>` : ''}
          </div>
          <button onclick="courseSearch.clearAllFilters()" style="padding: 0.8rem 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 25px; color: white; cursor: pointer; font-weight: 600;">
            Clear All Filters
          </button>
        </div>
      `;
      coursesGrid.appendChild(message);
    } else if (searchTerm || levelFilter || categoryFilter || priceFilter) {
      // Show results count message
      const message = document.createElement('div');
      message.className = 'filter-results-message';
      message.innerHTML = `
        <div style="text-align: center; padding: 1rem; margin-bottom: 2rem; background: rgba(255,255,255,0.1); border-radius: 15px; backdrop-filter: blur(10px);">
          <p style="color: rgba(255,255,255,0.9); margin: 0;">
            Found ${visibleCount} course${visibleCount !== 1 ? 's' : ''} 
            ${searchTerm ? `matching "${searchTerm}"` : 'with your filters'}
          </p>
        </div>
      `;
      coursesGrid.parentElement.insertBefore(message, coursesGrid);
    }
  }

  clearAllFilters() {
    // Clear all filter inputs
    if (this.searchInput) this.searchInput.value = '';
    const levelFilter = document.getElementById('levelFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceFilter = document.getElementById('priceFilter');
    
    if (levelFilter) levelFilter.value = '';
    if (categoryFilter) categoryFilter.value = '';
    if (priceFilter) priceFilter.value = '';
    
    // Clear suggestions and show all courses
    this.hideSuggestions();
    this.showAllCourses();
    
    // Remove any existing filter messages
    const existingMessage = document.querySelector('.filter-results-message');
    if (existingMessage) {
      existingMessage.remove();
    }
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

/*
 * Usage Examples for Extensible Search Configuration:
 * 
 * 1. Add new keywords dynamically:
 *    courseSearch.addKeywords(['blockchain', 'cryptocurrency', 'nft']);
 * 
 * 2. Remove specific keywords:
 *    courseSearch.removeKeywords(['outdated-tech', 'deprecated']);
 * 
 * 3. Update search configuration:
 *    courseSearch.updateConfig({
 *      maxSuggestions: 8,
 *      minQueryLength: 2,
 *      searchDelay: 500
 *    });
 * 
 * 4. Extract keywords from existing course content:
 *    const autoKeywords = courseSearch.extractKeywordsFromCourses();
 *    courseSearch.addKeywords(autoKeywords);
 * 
 * 5. Load keywords from external API:
 *    fetch('/api/search-keywords')
 *      .then(response => response.json())
 *      .then(keywords => courseSearch.addKeywords(keywords));
 * 
 * 6. Category-specific keyword management:
 *    const designKeywords = ['sketch', 'adobe xd', 'wireframing', 'prototyping'];
 *    courseSearch.addKeywords(designKeywords);
 */