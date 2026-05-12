# 🌟 AI RAG Support System

![Laravel](https://img.shields.io/badge/Laravel-13.x-red?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.5-blue?logo=php&logoColor=white)
![Ollama](https://img.shields.io/badge/Ollama-local-green)
![License](https://img.shields.io/badge/License-MIT-yellow.svg)

AI-powered support assistant built with Laravel, Ollama, PostgreSQL, pgvector, and Retrieval-Augmented Generation (RAG).

---

## 🚀 Overview

This project is a scalable AI support platform designed to provide intelligent, context-aware customer support using local LLMs and vector search.

Key capabilities include:

- Laravel backend architecture
- Local LLM inference using Ollama + Llama
- PostgreSQL with pgvector
- Retrieval-Augmented Generation (RAG)
- Semantic search and document retrieval
- AI-powered conversational support

> Built to work locally and scale toward enterprise-grade deployments.

---

## ✨ Features

### AI Support Chat
- Conversational support assistant
- Real-time chat interface
- Context-aware responses
- Clean support UI with vanilla JavaScript

### RAG (Retrieval-Augmented Generation)
- Semantic document retrieval
- Vector similarity search
- Prompt context injection
- Grounded AI responses using business data

### Local AI Infrastructure
- Runs locally with Ollama
- No external AI API dependency
- Privacy-friendly architecture
- Reduced operational cost

### Scalable Architecture
- Queue-based processing
- Modular services
- Extensible vector search layer

### Knowledge Base
- Support documentation indexing
- FAQ ingestion
- Product documentation support
- Internal knowledge retrieval

---

## 🧰 Tech Stack

### Backend
- Laravel
- PHP 8+

### AI / LLM
- Ollama
- Llama 3

### Database
- PostgreSQL
- pgvector

### Frontend
- Blade
- Vanilla JavaScript

### Infrastructure
- Laravel Queues
- Background Jobs
- REST APIs

---

## 🏗 Architecture

```text
User Question
      ↓
Laravel Application
      ↓
Embedding Generation
      ↓
pgvector Similarity Search
      ↓
Retrieve Relevant Chunks
      ↓
Inject Context into Prompt
      ↓
Llama 3 via Ollama
      ↓
AI Response
```

---

## 📁 Project Structure

```text
app/
├── Console/
├── Http/
│   └── Controllers/
├── Jobs/
├── Models/
├── Services/
│   ├── AI/
│   ├── Embeddings/
│   ├── Retrieval/
│   └── RAG/
├── Support/
└── Vector/

resources/
└── views/
    └── support/

database/
├── migrations/
└── seeders/
```

---

## 📌 Planned Components

### Chat System
- AI support interface
- Conversation memory
- Multi-session support
- Streaming responses

### Vector Search Engine
- pgvector integration
- Embedding indexing
- Top-K similarity search
- Metadata filtering

### Embedding Pipeline
- Document chunking
- Embedding generation
- Background indexing jobs
- Queue processing

### Knowledge Base Management
- File uploads
- PDF ingestion
- Markdown ingestion
- HTML extraction
- CMS synchronization

### AI Prompt Layer
- Prompt templates
- Context injection
- System prompt management
- Hallucination reduction

---

## 📈 Scalability Goals

This platform is built for future scale:

- Multi-tenant support
- Distributed vector infrastructure
- Dedicated AI inference servers
- Hybrid local/cloud LLM support
- Redis queue scaling
- Horizontal application scaling
- API-first architecture

---

## 💻 Local Development Setup

### Install Ollama

```bash
curl -fsSL https://ollama.com/install.sh | sh
```

### Pull Llama Model

```bash
ollama pull llama3
```

### Run Ollama

```bash
ollama run llama3
```

---

# PostgreSQL Setup

## Install PostgreSQL

```bash
brew install postgresql
```

## Start PostgreSQL

```bash
brew services start postgresql
```

---

# pgvector Setup

## Install pgvector

```bash
brew install pgvector
```

## Enable Extension

```sql
CREATE EXTENSION vector;
```

---

# Laravel Setup

## Install Dependencies

```bash
composer install
```

## Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

## Run Migrations

```bash
php artisan migrate
```

## Start Laravel

```bash
php artisan serve
```

---

# Current Status

## Completed
- Laravel AI support chat UI
- Ollama integration
- Local Llama inference
- AI conversational responses

## In Progress
- PostgreSQL integration
- pgvector support
- RAG pipeline implementation

## Planned
- Embedding generation
- Document ingestion
- Semantic retrieval
- Production deployment pipeline

---

# Vision

This project aims to evolve into a scalable AI-native support infrastructure capable of powering:

- Customer support assistants
- Internal AI knowledge bases
- AI help centers
- AI product assistants
- Enterprise RAG systems

---

## 📜 License

This project is licensed under the **MIT License**.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

```
