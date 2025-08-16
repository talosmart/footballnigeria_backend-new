# Football Nigeria Polls & Predictions API Documentation

## Overview

This API provides comprehensive poll, prediction, and betting tips functionality for the Football Nigeria website. It supports League Polls, National Polls, star ratings, multiple choice questions, and prediction systems.

## Base URL

```
http://your-domain.com/api
```

## Authentication

Most endpoints require authentication using Laravel Sanctum. Include the token in the Authorization header:

```
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## Polls API

### Get All Polls

**GET** `/polls`

**Query Parameters:**

-   `type` (optional): `league` or `national`
-   `poll_type` (optional): `multiple_choice`, `rating`, or `prediction`
-   `featured` (optional): `true` to get only featured polls
-   `per_page` (optional): Number of polls per page (default: 10)

**Response Example:**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "title": "WHO WAS THE MAN OF THE MATCH?",
            "description": "Vote for your choice of player who stood out on the pitch for Nigeria vs South Africa",
            "type": "league",
            "poll_type": "multiple_choice",
            "image": "http://domain.com/storage/polls/image.jpg",
            "is_active": true,
            "is_featured": true,
            "total_votes": 135,
            "options": [
                {
                    "id": 1,
                    "option_text": "Osimhen",
                    "vote_count": 45,
                    "percentage": 30.0
                },
                {
                    "id": 2,
                    "option_text": "Victor Moses",
                    "vote_count": 45,
                    "percentage": 30.0
                },
                {
                    "id": 3,
                    "option_text": "Chukwueze",
                    "vote_count": 45,
                    "percentage": 30.0
                }
            ],
            "has_user_voted": false,
            "user_vote": null
        }
    ],
    "meta": {
        "current_page": 1,
        "per_page": 10,
        "total": 5,
        "last_page": 1
    }
}
```

### Get League Polls Only

**GET** `/polls/league`

### Get National Polls Only

**GET** `/polls/national`

### Get Single Poll

**GET** `/polls/{id}`

### Cast Vote

**POST** `/polls/vote` (Requires Authentication)

**Request Body:**

```json
{
    "poll_id": 1,
    "vote_type": "option",
    "poll_option_id": 1
}
```

For rating polls:

```json
{
    "poll_id": 2,
    "vote_type": "rating",
    "rating_value": 4
}
```

For prediction polls:

```json
{
    "poll_id": 3,
    "vote_type": "prediction",
    "prediction_text": "Nigeria will win 2-1"
}
```

### Create Poll (Admin Only)

**POST** `/polls/create` (Requires Admin Authentication)

**Request Body (multipart/form-data):**

```json
{
    "title": "Who will be Nigeria's top scorer?",
    "description": "Predict who will score the most goals",
    "type": "national",
    "poll_type": "multiple_choice",
    "image": "file upload",
    "is_featured": true,
    "start_date": "2025-08-20",
    "end_date": "2025-09-20",
    "options": [
        { "text": "Osimhen" },
        { "text": "Lookman" },
        { "text": "Iheanacho" }
    ]
}
```

### Update Poll (Admin Only)

**PUT** `/polls/update` (Requires Admin Authentication)

### Delete Poll (Admin Only)

**DELETE** `/polls/{id}` (Requires Admin Authentication)

### Get Poll Statistics (Admin Only)

**GET** `/polls/{id}/stats` (Requires Admin Authentication)

---

## Predictions API

### Get All Predictions

**GET** `/predictions`

**Query Parameters:**

-   `type` (optional): `match`, `tournament`, `player_performance`, or `season`
-   `resolved` (optional): `true` or `false`
-   `per_page` (optional): Number of predictions per page (default: 10)

**Response Example:**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "title": "Nigeria vs Ghana Match Result",
            "description": "Predict the outcome of this crucial World Cup qualifier",
            "type": "match",
            "home_team": "Nigeria",
            "away_team": "Ghana",
            "tournament": "World Cup Qualifiers",
            "event_date": "2025-08-22T15:00:00Z",
            "prediction_options": {
                "options": ["Nigeria Win", "Draw", "Ghana Win"],
                "score_prediction": true,
                "goal_scorer": true
            },
            "prediction_deadline": "2025-08-21T15:00:00Z",
            "is_active": true,
            "is_resolved": false,
            "has_user_predicted": false,
            "user_prediction": null
        }
    ]
}
```

### Get Single Prediction

**GET** `/predictions/{id}`

### Submit Prediction

**POST** `/predictions/submit` (Requires Authentication)

**Request Body:**

```json
{
    "prediction_id": 1,
    "predicted_result": {
        "winner": "Nigeria",
        "score": "2-1",
        "first_goalscorer": "Osimhen"
    }
}
```

### Create Prediction (Admin Only)

**POST** `/predictions/create` (Requires Admin Authentication)

**Request Body:**

```json
{
    "title": "AFCON 2024 Winner",
    "description": "Who will win AFCON 2024?",
    "type": "tournament",
    "tournament": "AFCON 2024",
    "event_date": "2025-10-15",
    "prediction_options": {
        "teams": ["Nigeria", "Egypt", "Morocco", "Senegal"]
    },
    "prediction_deadline": "2025-09-15"
}
```

### Resolve Prediction (Admin Only)

**POST** `/predictions/resolve` (Requires Admin Authentication)

**Request Body:**

```json
{
    "prediction_id": 1,
    "actual_result": {
        "winner": "Nigeria",
        "score": "2-1",
        "first_goalscorer": "Osimhen"
    }
}
```

### Get Leaderboard

**GET** `/predictions/leaderboard`

---

## Tips API

### Get All Tips

**GET** `/tips`

**Query Parameters:**

-   `status` (optional): `pending`, `won`, `lost`, or `void`
-   `confidence` (optional): `low`, `medium`, or `high`
-   `premium` (optional): `true` for premium tips only
-   `featured` (optional): `true` for featured tips only
-   `per_page` (optional): Number of tips per page (default: 15)

**Response Example:**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "title": "Nigeria vs Ghana - Both Teams to Score",
            "description": "Both teams have strong attacking records...",
            "match_teams": "Nigeria vs Ghana",
            "match_date": "2025-08-22T15:00:00Z",
            "odds": 1.85,
            "tip_type": "both_teams_score",
            "recommended_bet": "Both Teams to Score - Yes",
            "confidence_level": "high",
            "is_premium": false,
            "is_featured": true,
            "status": "pending"
        }
    ]
}
```

### Get Single Tip

**GET** `/tips/{id}`

### Create Tip (Admin Only)

**POST** `/tips/create` (Requires Admin Authentication)

**Request Body:**

```json
{
    "title": "Nigeria Win vs South Africa",
    "description": "Nigeria has a strong home record...",
    "match_teams": "Nigeria vs South Africa",
    "match_date": "2025-08-25",
    "odds": 2.1,
    "tip_type": "win",
    "recommended_bet": "Nigeria to Win",
    "confidence_level": "medium",
    "is_premium": false,
    "is_featured": true
}
```

### Update Tip Status (Admin Only)

**PUT** `/tips/status` (Requires Admin Authentication)

**Request Body:**

```json
{
    "tip_id": 1,
    "status": "won"
}
```

### Get Tip Statistics

**GET** `/tips/stats`

---

## Error Responses

All endpoints return errors in the following format:

```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

## HTTP Status Codes

-   `200` - Success
-   `422` - Validation Error
-   `401` - Unauthorized
-   `403` - Forbidden
-   `404` - Not Found
-   `500` - Server Error

---

## Implementation Examples

### Frontend Integration Examples

#### Displaying League Polls

```javascript
// Fetch league polls
fetch("/api/polls/league?featured=true")
    .then((response) => response.json())
    .then((data) => {
        data.data.forEach((poll) => {
            console.log(poll.title, poll.options);
        });
    });
```

#### Casting a Vote

```javascript
// Vote on a poll
fetch("/api/polls/vote", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        Authorization: "Bearer " + token,
    },
    body: JSON.stringify({
        poll_id: 1,
        vote_type: "option",
        poll_option_id: 2,
    }),
})
    .then((response) => response.json())
    .then((data) => {
        console.log("Vote cast successfully:", data);
    });
```

#### Star Rating System

```javascript
// Submit a star rating
fetch("/api/polls/vote", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        Authorization: "Bearer " + token,
    },
    body: JSON.stringify({
        poll_id: 5,
        vote_type: "rating",
        rating_value: 4,
    }),
})
    .then((response) => response.json())
    .then((data) => {
        console.log("Rating submitted:", data);
    });
```

This comprehensive API matches the design shown in your image and provides all the functionality needed for polls, predictions, and tips on your Football Nigeria website.
