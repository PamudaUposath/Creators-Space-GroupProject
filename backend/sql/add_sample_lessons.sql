-- Add sample lessons for enrolled courses
INSERT IGNORE INTO lessons (course_id, title, content, video_url, position, duration, is_free, is_published) VALUES
-- Course 2: UI/UX Design Fundamentals
(2, 'Introduction to UI/UX Design', 'Learn the fundamentals of user interface and user experience design.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/uiux_intro.mp4', 1, '30 minutes', 1, 1),
(2, 'Design Principles', 'Understanding the core principles of good design.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/design_principles.mp4', 2, '45 minutes', 1, 1),
(2, 'Figma Basics', 'Getting started with Figma for UI design.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/figma_basics.mp4', 3, '1 hour', 0, 1),

-- Course 3: JavaScript in 30 Days
(3, 'JavaScript Fundamentals', 'Variables, functions, and basic syntax.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/js_fundamentals.mp4', 1, '35 minutes', 1, 1),
(3, 'DOM Manipulation', 'Working with the Document Object Model.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/dom_manipulation.mp4', 2, '50 minutes', 1, 1),
(3, 'Async JavaScript', 'Promises, async/await, and API calls.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/async_js.mp4', 3, '1 hour 15 minutes', 0, 1),

-- Course 8: Vue.js Complete Guide
(8, 'Vue.js Introduction', 'Getting started with Vue.js framework.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/vue_intro.mp4', 1, '40 minutes', 1, 1),
(8, 'Vue Components', 'Building reusable Vue components.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/vue_components.mp4', 2, '55 minutes', 0, 1),
(8, 'Vue Router & Vuex', 'State management and routing in Vue.js.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/vue_advanced.mp4', 3, '1 hour 20 minutes', 0, 1),

-- Course 9: Angular Enterprise Development
(9, 'Angular Setup', 'Setting up Angular development environment.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/angular_setup.mp4', 1, '25 minutes', 1, 1),
(9, 'Angular Components', 'Creating and managing Angular components.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/angular_components.mp4', 2, '45 minutes', 0, 1),
(9, 'Angular Services', 'Dependency injection and services.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/angular_services.mp4', 3, '50 minutes', 0, 1),

-- Course 11: GraphQL API Development
(11, 'GraphQL Basics', 'Introduction to GraphQL concepts.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/graphql_basics.mp4', 1, '35 minutes', 1, 1),
(11, 'GraphQL Schema', 'Designing GraphQL schemas and types.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/graphql_schema.mp4', 2, '40 minutes', 0, 1),
(11, 'GraphQL Resolvers', 'Implementing resolvers and mutations.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/graphql_resolvers.mp4', 3, '55 minutes', 0, 1),

-- Course 54: Design of algorithms
(54, 'Algorithm Fundamentals', 'Introduction to algorithm design and analysis.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/algo_fundamentals.mp4', 1, '45 minutes', 1, 1),
(54, 'Sorting Algorithms', 'Understanding different sorting techniques.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/sorting_algos.mp4', 2, '60 minutes', 0, 1),
(54, 'Dynamic Programming', 'Advanced algorithm design patterns.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/dynamic_programming.mp4', 3, '75 minutes', 0, 1),

-- Course 57: Data Science with Python
(57, 'Python for Data Science', 'Python libraries for data analysis.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/python_datascience.mp4', 1, '50 minutes', 1, 1),
(57, 'Pandas and NumPy', 'Data manipulation with Pandas and NumPy.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/pandas_numpy.mp4', 2, '65 minutes', 0, 1),
(57, 'Data Visualization', 'Creating charts and graphs with Matplotlib.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/data_visualization.mp4', 3, '55 minutes', 0, 1);